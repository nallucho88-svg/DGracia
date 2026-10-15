<?php

namespace App\Controllers;

use App\Models\DeliveryZoneModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;

class Checkout extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $cart = $this->session->get('cart') ?? [];
        
        if (empty($cart)) {
            return redirect()->to(base_url('carrito'))->with('error', 'Tu carrito está vacío.');
        }

        $zoneModel = new DeliveryZoneModel();
        $zones = $zoneModel->where('status', 1)->findAll();

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['subtotal'];
        }

        // Datos del cliente si está autenticado
        $userData = null;
        if (session()->get('isLoggedIn') && session()->get('role') === 'customer') {
            $userModel = new \App\Models\UserModel();
            $userData = $userModel->find(session()->get('user_id'));
        }

        $appliedCoupon = session()->get('applied_coupon') ?? null;
        $discountService = new \App\Libraries\DiscountService();
        $summary = $discountService->calculateSummary($cart, $appliedCoupon);

        $data = [
            'title'          => 'Checkout | Dulce Gracia',
            'cart'           => $cart,
            'summary'        => $summary,
            'appliedCoupon'  => $appliedCoupon,
            'zones'          => $zones,
            'userData'       => $userData
        ];

        return view('checkout/index', $data);
    }

    public function process()
    {
        $cart = $this->session->get('cart') ?? [];
        if (empty($cart)) {
            return redirect()->to(base_url('carrito'));
        }

        $rules = [
            'customer_name'  => 'required|min_length[3]',
            'phone'          => 'required|min_length[7]',
            'customer_email' => 'permit_empty|valid_email',
            'delivery_type'  => 'required|in_list[domicilio,recoger]',
            'payment_method' => 'required'
        ];

        if ($this->request->getPost('delivery_type') === 'domicilio') {
            $rules['address'] = 'required';
            $rules['delivery_zone_id'] = 'required|numeric';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $zoneModel      = new DeliveryZoneModel();
        $orderModel     = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $productModel   = new \App\Models\ProductModel();
        $discountService = new \App\Libraries\DiscountService();

        $deliveryCost = 0.0;
        $zoneId = $this->request->getPost('delivery_zone_id');
        if ($this->request->getPost('delivery_type') === 'domicilio' && $zoneId) {
            $zone = $zoneModel->find($zoneId);
            if ($zone) {
                $deliveryCost = (float) $zone->cost;
            }
        }

        $appliedCoupon = session()->get('applied_coupon') ?? null;
        // Centralized Server-Side Recalculation (Security Rule)
        $summary = $discountService->calculateSummary($cart, $appliedCoupon, $deliveryCost);

        $userId = session()->get('isLoggedIn') && session()->get('role') === 'customer' ? session()->get('user_id') : null;
        $orderNumber = 'DG-' . date('ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 5));

        // Puntos a ganar
        $pointsSystemEnabled = (setting('points_system_enabled', '1') === '1');
        $pointsEarned = 0;

        if ($userId && $pointsSystemEnabled) {
            $purchaseReq = (float) setting('point_purchase_amount', '1000');
            $pointsGiven = (int) setting('point_reward_qty', '1');
            if ($purchaseReq > 0) {
                $pointsEarned = (int) (floor($summary['subtotal_after_product_discount'] / $purchaseReq) * $pointsGiven);
            }
        }

        $orderData = [
            'order_number'          => $orderNumber,
            'user_id'               => $userId,
            'customer_name'         => trim((string)$this->request->getPost('customer_name')),
            'phone'                 => trim((string)$this->request->getPost('phone')),
            'whatsapp'              => trim((string)$this->request->getPost('whatsapp')),
            'customer_email'        => strtolower(trim((string)$this->request->getPost('customer_email'))),
            'address'               => trim((string)$this->request->getPost('address')),
            'neighborhood'          => trim((string)$this->request->getPost('neighborhood')),
            'city'                  => trim((string)$this->request->getPost('city')) ?: 'Medellín',
            'delivery_instructions' => trim((string)$this->request->getPost('delivery_instructions')),
            'delivery_date'         => date('Y-m-d'),
            'delivery_time'         => '',
            'delivery_type'         => $this->request->getPost('delivery_type'),
            'delivery_zone_id'      => $zoneId ?: null,
            'delivery_cost'         => $summary['delivery_cost'],
            'coupon_code'           => $summary['coupon_code'],
            'coupon_discount'       => $summary['coupon_discount_total'],
            'discount_amount'       => $summary['product_discount_total'],
            'subtotal'              => $summary['subtotal_after_product_discount'],
            'total'                 => $summary['final_total'],
            'payment_method'        => $this->request->getPost('payment_method'),
            'payment_status'        => 'pendiente',
            'status'                => 'pendiente',
            'source'                => 'web',
            'notes'                 => trim((string)$this->request->getPost('notes'))
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        $orderId = $orderModel->insert($orderData);

        // Insertar items conservando precios e información histórica
        foreach ($cart as $item) {
            $prod = $productModel->find($item['id']);
            $sku  = $prod ? ($prod->slug ?? 'PRD-'.$item['id']) : 'PRD-'.$item['id'];

            $orderItemModel->insert([
                'order_id'        => $orderId,
                'product_id'      => $item['id'],
                'product_name'    => $item['name'],
                'sku'             => $sku,
                'original_price'  => $item['normal_price'] ?? $item['price'],
                'offer_price'     => !empty($item['has_discount']) ? $item['price'] : null,
                'discount_amount' => $item['discount_amount'] ?? 0.00,
                'price'           => $item['price'],
                'quantity'        => $item['quantity'],
                'subtotal'        => $item['subtotal']
            ]);
        }

        // Puntos para cliente autenticado
        if ($userId && $pointsEarned > 0) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($userId);
            if ($user) {
                $newPoints = (int) ($user->points ?? 0) + $pointsEarned;
                $userModel->update($userId, ['points' => $newPoints]);
                session()->set('points', $newPoints);

                $ptModel = new \App\Models\PointTransactionModel();
                $ptModel->insert([
                    'user_id'     => $userId,
                    'order_id'    => $orderId,
                    'points'      => $pointsEarned,
                    'type'        => 'earned',
                    'description' => 'Compra en línea #' . $orderNumber
                ]);
            }
        }

        // Registrar uso de cupón en coupon_logs
        $couponCode = $summary['coupon_code'] ?? null;
        if ($couponCode && !empty($appliedCoupon['coupon'])) {
            $cModel = new \App\Models\CouponModel();
            $cModel->where('code', $couponCode)->set('used_count', 'used_count+1', false)->update();

            $clModel = new \CodeIgniter\Model();
            $db->table('coupon_logs')->insert([
                'coupon_id'  => $appliedCoupon['coupon']->id,
                'user_id'    => $userId,
                'order_id'   => $orderId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Ocurrió un problema al procesar tu pedido. Por favor intenta nuevamente.');
        }

        // Limpiar sesión y carrito en BD
        $this->session->remove('cart');
        $this->session->remove('applied_coupon');
        if ($userId) {
            $userCartModel = new \App\Models\UserCartModel();
            $userCartModel->clearUserCart($userId);
        }

        // Notificación interna en el panel Admin
        \App\Models\NotificationModel::createNotification(
            'order',
            '🛍️ Nuevo Pedido #' . $orderNumber,
            'Pedido por $' . number_format($summary['final_total'], 0, ',', '.') . ' registrado por ' . $orderData['customer_name'],
            base_url('admin/pedidos/ver/' . $orderId)
        );

        // Notificaciones por correo a administradores y cliente
        try {
            $createdOrder = $orderModel->find($orderId);
            $createdItems = $orderItemModel->where('order_id', $orderId)->findAll();
            if ($createdOrder && !empty($createdItems)) {
                // 1. Correo de nueva compra a administradores (FASE 5)
                \App\Libraries\EmailService::sendOrderNotificationToAdmins($createdOrder, $createdItems);
                // 2. Correo de compra al cliente con enlace de seguimiento (FASE 4)
                \App\Libraries\EmailService::sendOrderConfirmationToCustomer($createdOrder, $createdItems);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error disparando correo de checkout: ' . $e->getMessage());
        }

        return redirect()->to(base_url('pedido/confirmacion/' . $orderNumber));
    }

    public function success($orderNumber)
    {
        $orderModel     = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $ratingModel    = new \App\Models\RatingModel();

        $order = $orderModel->where('order_number', $orderNumber)->first();

        if (!$order) {
            return redirect()->to(base_url());
        }

        $items = $orderItemModel->where('order_id', $order->id)->findAll();
        foreach ($items as &$item) {
            $item->rating = $ratingModel->where('order_item_id', $item->id)->first();
        }

        $statuses = [
            'pendiente'     => ['label' => 'Pedido Recibido', 'icon' => 'fa-clipboard-check', 'step' => 1],
            'en preparacion'=> ['label' => 'En Preparación', 'icon' => 'fa-blender', 'step' => 2],
            'enviado'       => ['label' => 'En Camino', 'icon' => 'fa-motorcycle', 'step' => 3],
            'entregado'     => ['label' => 'Entregado', 'icon' => 'fa-check-circle', 'step' => 4],
            'cancelado'     => ['label' => 'Cancelado', 'icon' => 'fa-times-circle', 'step' => 0]
        ];

        $currentStep = $statuses[$order->status]['step'] ?? 1;

        $data = [
            'title'       => 'Pedido Confirmado | Dulce Gracia',
            'order'       => $order,
            'items'       => $items,
            'statuses'    => $statuses,
            'currentStep' => $currentStep
        ];

        return view('checkout/success', $data);
    }
}
