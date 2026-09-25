<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;
use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\PointTransactionModel;
use App\Libraries\DiscountService;

class Pos extends BaseController
{
    public function index()
    {
        $productModel  = new ProductModel();
        $categoryModel = new CategoryModel();
        $imageModel    = new ProductImageModel();
        $userModel     = new UserModel();
        $discountService = new DiscountService();

        $categories = $categoryModel->where('status', 1)->findAll();
        $rawProducts = $productModel->where('status', 1)->where('availability', 1)->findAll();
        $customers   = $userModel->where('role', 'customer')->findAll();

        $products = [];
        foreach ($rawProducts as $p) {
            $img = $imageModel->where('product_id', $p->id)->where('is_main', 1)->first();
            $p->main_image = media_url($img ? $img->image_path : '', 'assets/img/placeholder.jpg');
            $p->discount_info = $discountService->calculateProductDiscount($p, false);
            $products[] = $p;
        }

        // Obtener descuento cliente fiel por defecto (ej. 10%)
        $loyalDiscountPct = (float) setting('loyalty_discount_percentage', '10');

        $data = [
            'title'            => 'Punto de Venta / Caja (POS) | Admin',
            'categories'       => $categories,
            'products'         => $products,
            'customers'        => $customers,
            'loyalDiscountPct' => $loyalDiscountPct
        ];

        return view('admin/pos/index', $data);
    }

    /**
     * Buscar clientes registrados vía AJAX
     */
    public function searchCustomers()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $q = trim($this->request->getGet('q') ?? '');
        $userModel = new UserModel();

        if (empty($q)) {
            $customers = $userModel->where('role', 'customer')->limit(10)->findAll();
        } else {
            $customers = $userModel->where('role', 'customer')
                ->groupStart()
                    ->like('name', $q)
                    ->orLike('lastname', $q)
                    ->orLike('email', $q)
                    ->orLike('phone', $q)
                ->groupEnd()
                ->limit(20)
                ->findAll();
        }

        return $this->response->setJSON($customers);
    }

    /**
     * Validar cupón en POS vía AJAX
     */
    public function validateCoupon()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $code       = trim($this->request->getPost('code') ?? '');
        $subtotal   = (float)$this->request->getPost('subtotal');
        $customerId = (int)$this->request->getPost('customer_id');

        $userModel = new UserModel();
        $customer = $customerId ? $userModel->find($customerId) : null;
        $isLoyal = $customer ? (bool)$customer->is_loyal : false;

        $discountService = new DiscountService();
        $result = $discountService->validateCoupon($code, $subtotal, $customerId, $isLoyal);

        return $this->response->setJSON($result);
    }

    /**
     * Procesar la Venta de Caja POS
     */
    public function processSale()
    {
        if (!$this->request->isAJAX() || strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Petición no permitida']);
        }

        $itemsJson = $this->request->getPost('items');
        $items     = json_decode($itemsJson, true);

        if (empty($items) || !is_array($items)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'La venta no contiene productos.']);
        }

        $customerId     = (int) $this->request->getPost('customer_id');
        $paymentMethod  = $this->request->getPost('payment_method') ?: 'Efectivo';
        $applyLoyalty   = (bool) $this->request->getPost('apply_loyalty_discount');
        $couponCode     = strtoupper(trim($this->request->getPost('coupon_code') ?? ''));

        $userModel      = new UserModel();
        $productModel   = new ProductModel();
        $orderModel     = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $discountService = new DiscountService();

        $customer = $customerId ? $userModel->find($customerId) : null;

        $subtotal = 0;
        $productDiscountTotal = 0;
        $orderItemsData = [];

        foreach ($items as $it) {
            $product = $productModel->find($it['id']);
            if (!$product) continue;

            $qty         = max(1, (int)$it['qty']);
            $unitPrice   = (float) $it['price'];
            $normalPrice = isset($it['normal_price']) ? (float)$it['normal_price'] : (float)$product->price;
            $discUnit    = max(0, $normalPrice - $unitPrice);

            $itemSub     = $unitPrice * $qty;
            $subtotal   += $itemSub;
            $productDiscountTotal += ($discUnit * $qty);

            $orderItemsData[] = [
                'product_id'      => $product->id,
                'product_name'    => $product->name,
                'sku'             => $product->slug ?? ('PRD-' . $product->id),
                'original_price'  => $normalPrice,
                'offer_price'     => ($discUnit > 0) ? $unitPrice : null,
                'discount_amount' => $discUnit,
                'price'           => $unitPrice,
                'quantity'        => $qty,
                'subtotal'        => $itemSub
            ];
        }

        // Validar cupón en servidor si viene código
        $couponDiscountTotal = 0;
        $appliedCouponObj = null;
        if (!empty($couponCode)) {
            $cRes = $discountService->validateCoupon($couponCode, $subtotal, $customer ? $customer->id : 0, $customer ? (bool)$customer->is_loyal : false);
            if ($cRes['valid']) {
                $couponDiscountTotal = (float)$cRes['discount_amount'];
                $appliedCouponObj = $cRes['coupon'];
            }
        }

        // Aplicar Descuento Adicional Cliente Fiel si aplica
        $loyaltyDiscountAmount = 0;
        if ($customer && ($customer->is_loyal || $applyLoyalty)) {
            $loyalPct = (float) setting('loyalty_discount_percentage', '10');
            $loyaltyDiscountAmount = max(0, $subtotal - $couponDiscountTotal) * ($loyalPct / 100);
        }

        $totalDiscountAmount = $couponDiscountTotal + $loyaltyDiscountAmount;
        $total = max(0, $subtotal - $totalDiscountAmount);
        $orderNumber = 'POS-' . date('ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));

        // Puntos a generar
        $pointsEarned = 0;
        if ($customer && setting('points_system_enabled', '1') === '1') {
            $purchaseReq = (float) setting('point_purchase_amount', '1000');
            $pointsGiven = (int) setting('point_reward_qty', '1');
            if ($purchaseReq > 0) {
                $pointsEarned = (int) (floor($total / $purchaseReq) * $pointsGiven);
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Crear Orden en BD
        $orderId = $orderModel->insert([
            'order_number'          => $orderNumber,
            'user_id'               => $customer ? $customer->id : null,
            'customer_name'         => $customer ? trim($customer->name . ' ' . ($customer->lastname ?? '')) : 'Cliente Mostrador / Caja',
            'phone'                 => $customer ? ($customer->phone ?: 'N/A') : 'N/A',
            'whatsapp'              => $customer ? ($customer->whatsapp ?: '') : '',
            'address'               => 'Venta Directa en Tienda (POS)',
            'delivery_type'         => 'recoger',
            'delivery_cost'         => 0.00,
            'coupon_code'           => !empty($couponCode) ? $couponCode : null,
            'coupon_discount'       => $couponDiscountTotal,
            'discount_amount'       => $productDiscountTotal + $loyaltyDiscountAmount,
            'subtotal'              => $subtotal,
            'total'                 => $total,
            'payment_method'        => $paymentMethod,
            'status'                => 'entregado',
            'source'                => 'pos',
            'notes'                 => 'Venta en caja registradora'
        ]);

        // 2. Crear Items
        foreach ($orderItemsData as $itData) {
            $itData['order_id'] = $orderId;
            $orderItemModel->insert($itData);
        }

        // 3. Registrar uso de cupón en coupon_logs
        if (!empty($couponCode) && $appliedCouponObj) {
            $cModel = new \App\Models\CouponModel();
            $cModel->where('code', $couponCode)->set('used_count', 'used_count+1', false)->update();

            $db->table('coupon_logs')->insert([
                'coupon_id'  => $appliedCouponObj->id,
                'user_id'    => $customer ? $customer->id : null,
                'order_id'   => $orderId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // 4. Generar Puntos para el cliente registrado
        if ($customer && $pointsEarned > 0) {
            $newPoints = (int)($customer->points ?? 0) + $pointsEarned;
            $userModel->update($customer->id, ['points' => $newPoints]);

            $ptModel = new PointTransactionModel();
            $ptModel->insert([
                'user_id'     => $customer->id,
                'order_id'    => $orderId,
                'points'      => $pointsEarned,
                'type'        => 'earned',
                'description' => 'Venta en Caja POS #' . $orderNumber
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error al procesar la venta en la base de datos. Transaction ROLLBACK.']);
        }

        return $this->response->setJSON([
            'status'        => 'success',
            'message'       => '¡Venta en caja registrada exitosamente!',
            'order_number'  => $orderNumber,
            'total'         => '$' . number_format($total, 0, ',', '.'),
            'points_earned' => $pointsEarned
        ]);
    }
}
