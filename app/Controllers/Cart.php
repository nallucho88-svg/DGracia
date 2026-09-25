<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Cart extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $cart = $this->session->get('cart') ?? [];
        $appliedCoupon = $this->session->get('applied_coupon') ?? null;

        $discountService = new \App\Libraries\DiscountService();
        $summary = $discountService->calculateSummary($cart, $appliedCoupon);

        $data = [
            'title'         => 'Tu Carrito | Dulce Gracia',
            'cart'          => $cart,
            'summary'       => $summary,
            'appliedCoupon' => $appliedCoupon,
            'total'         => $summary['final_total']
        ];

        return view('cart/index', $data);
    }

    public function add()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $productId = (int) $this->request->getPost('product_id');
        $qty       = max(1, (int) ($this->request->getPost('quantity') ?? 1));

        $productModel = new ProductModel();
        $product = $productModel->find($productId);

        if (!$product || !$product->availability || $product->status == 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Producto no disponible']);
        }

        $discountService = new \App\Libraries\DiscountService();
        $isLoyal = (bool) (session()->get('is_loyal') ?? false);
        $discountInfo = $discountService->calculateProductDiscount($product, $isLoyal);

        $unitPrice   = (float) $discountInfo['final_price'];
        $normalPrice = (float) $discountInfo['original_price'];

        $cart = $this->session->get('cart') ?? [];
        
        $found = false;
        foreach ($cart as &$item) {
            if ($item['id'] == $product->id) {
                $item['quantity'] += $qty;
                $item['subtotal'] = $item['quantity'] * $unitPrice;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $imageModel = new \App\Models\ProductImageModel();
            $image = $imageModel->where('product_id', $product->id)->where('is_main', 1)->first();
            $imagePath = $image ? media_url($image->image_path) : media_url('assets/img/placeholder.jpg');

            $cart[] = [
                'id'               => $product->id,
                'name'             => $product->name,
                'price'            => $unitPrice,
                'normal_price'     => $normalPrice,
                'offer_price'      => $discountInfo['has_discount'] ? $unitPrice : null,
                'discount_amount'  => (float) $discountInfo['discount_amount'],
                'has_discount'     => (bool) $discountInfo['has_discount'],
                'discount_percent' => $discountInfo['discount_percentage'],
                'quantity'         => $qty,
                'subtotal'         => $unitPrice * $qty,
                'image'            => $imagePath
            ];
        }

        $this->session->set('cart', $cart);

        // Persistir en BD si está autenticado
        if (session()->get('isLoggedIn') && session()->get('role') === 'customer') {
            $userCartModel = new \App\Models\UserCartModel();
            $userCartModel->saveSessionCart(session()->get('user_id'), $cart);
        }

        $count = array_sum(array_column($cart, 'quantity'));

        return $this->response->setJSON([
            'status'     => 'success', 
            'message'    => 'Producto agregado', 
            'cart_count' => $count
        ]);
    }

    public function update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $productId = (int) $this->request->getPost('product_id');
        $qty       = (int) $this->request->getPost('quantity');

        $cart = $this->session->get('cart') ?? [];
        
        $total = 0;
        $subtotal = 0;

        foreach ($cart as $key => &$item) {
            if ($item['id'] == $productId) {
                if ($qty <= 0) {
                    unset($cart[$key]);
                    continue;
                }
                $item['quantity'] = $qty;
                $item['subtotal'] = $item['quantity'] * $item['price'];
                $subtotal = $item['subtotal'];
            }
            $total += $item['subtotal'];
        }

        $cart = array_values($cart);
        $this->session->set('cart', $cart);

        // Persistir en BD si está autenticado
        if (session()->get('isLoggedIn') && session()->get('role') === 'customer') {
            $userCartModel = new \App\Models\UserCartModel();
            $userCartModel->saveSessionCart(session()->get('user_id'), $cart);
        }

        $appliedCoupon = $this->session->get('applied_coupon') ?? null;
        $discountService = new \App\Libraries\DiscountService();
        $summary = $discountService->calculateSummary($cart, $appliedCoupon);

        $count = array_sum(array_column($cart, 'quantity'));

        return $this->response->setJSON([
            'status'        => 'success',
            'item_subtotal' => $subtotal,
            'cart_total'    => $summary['final_total'],
            'summary'       => $summary,
            'cart_count'    => $count
        ]);
    }

    public function remove()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $productId = (int) $this->request->getPost('product_id');
        $cart = $this->session->get('cart') ?? [];
        
        foreach ($cart as $key => $item) {
            if ($item['id'] == $productId) {
                unset($cart[$key]);
                break;
            }
        }
        
        $cart = array_values($cart);
        $this->session->set('cart', $cart);

        // Persistir en BD si está autenticado
        if (session()->get('isLoggedIn') && session()->get('role') === 'customer') {
            $userCartModel = new \App\Models\UserCartModel();
            $userCartModel->saveSessionCart(session()->get('user_id'), $cart);
        }

        $appliedCoupon = $this->session->get('applied_coupon') ?? null;
        $discountService = new \App\Libraries\DiscountService();
        $summary = $discountService->calculateSummary($cart, $appliedCoupon);

        $count = array_sum(array_column($cart, 'quantity'));

        return $this->response->setJSON([
            'status'     => 'success',
            'cart_total' => $summary['final_total'],
            'summary'    => $summary,
            'cart_count' => $count
        ]);
    }

    public function getMiniCart()
    {
        $cart = $this->session->get('cart') ?? [];
        $appliedCoupon = $this->session->get('applied_coupon') ?? null;
        $discountService = new \App\Libraries\DiscountService();
        $summary = $discountService->calculateSummary($cart, $appliedCoupon);

        foreach ($cart as &$item) {
            $item['normal_price_formatted'] = '$' . number_format($item['normal_price'] ?? $item['price'], 0, ',', '.');
            $item['price_formatted']        = '$' . number_format($item['price'], 0, ',', '.');
            $item['subtotal_formatted']     = '$' . number_format($item['subtotal'], 0, ',', '.');
        }

        $count = array_sum(array_column($cart, 'quantity'));

        return $this->response->setJSON([
            'status'            => 'success',
            'cart'              => $cart,
            'total'             => $summary['final_total'],
            'total_formatted'   => $summary['formatted']['final_total'],
            'summary'           => $summary,
            'cart_count'        => $count
        ]);
    }

    public function applyCoupon()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $code = $this->request->getPost('code');
        $cart = $this->session->get('cart') ?? [];

        if (empty($cart)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'El carrito está vacío.']);
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['subtotal'];
        }

        $discountService = new \App\Libraries\DiscountService();
        $isLoyal = session()->get('is_loyal') ?? false;
        $userId  = session()->get('user_id') ?? 0;

        $result = $discountService->validateCoupon($code, $subtotal, $userId, $isLoyal);

        if ($result['valid']) {
            $appliedCoupon = [
                'code' => $result['code'],
                'discount_amount' => $result['discount_amount'],
                'formatted_amount' => $result['formatted_amount']
            ];
            $this->session->set('applied_coupon', $appliedCoupon);

            $summary = $discountService->calculateSummary($cart, $appliedCoupon);

            return $this->response->setJSON([
                'status'              => 'success',
                'message'             => $result['message'],
                'discount_amount'     => $result['discount_amount'],
                'discount_formatted'  => $result['formatted_amount'],
                'new_total'           => $summary['final_total'],
                'new_total_formatted' => $summary['formatted']['final_total'],
                'summary'             => $summary
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $result['message']
            ]);
        }
    }
}
