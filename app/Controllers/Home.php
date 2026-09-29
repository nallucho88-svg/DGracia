<?php

namespace App\Controllers;

use App\Libraries\DiscountService;

class Home extends BaseController
{
    public function index(): string
    {
        $bannerModel  = new \App\Models\BannerModel();
        $promoModel   = new \App\Models\PromotionModel();
        $productModel = new \App\Models\ProductModel();

        // Banners para el hero/slider
        $banners = $bannerModel->where('status', 1)
                               ->orderBy('priority', 'ASC')
                               ->findAll();

        // Promoción modal (popup de bienvenida)
        $modalPromo = $promoModel->where('type', 'modal')
                                 ->where('status', 1)
                                 ->orderBy('created_at', 'DESC')
                                 ->first();

        // Promociones flotantes (toasts)
        $floatingPromos = $promoModel->where('type', 'flotante')
                                     ->where('status', 1)
                                     ->findAll();

        // Productos destacados reales (disponibles, activos, imagen presente)
        $featuredProducts = $productModel
            ->where('status', 1)
            ->where('availability', 1)
            ->orderBy('created_at', 'DESC')
            ->limit(6)
            ->findAll();

        // Calcular descuentos con DiscountService e imágen principal
        $discountService = new DiscountService();
        $imageModel      = new \App\Models\ProductImageModel();
        $isLoyal         = (bool) (session()->get('is_loyal') ?? false);

        foreach ($featuredProducts as &$prod) {
            $mainImg = $imageModel->where('product_id', $prod->id)->where('is_main', 1)->first();
            $prod->image_path = $mainImg ? $mainImg->image_path : '';
            $prod->discount_info = $discountService->calculateProductDiscount($prod, $isLoyal);
        }
        unset($prod);

        $siteName = setting('site_name', 'Dulce Gracia');

        $data = [
            'title'           => 'Inicio | ' . $siteName,
            'banners'         => $banners,
            'modalPromo'      => $modalPromo,
            'floatingPromos'  => $floatingPromos,
            'featuredProducts'=> $featuredProducts,
        ];
        return view('home/index', $data);
    }

    public function about(): string
    {
        $orderModel   = new \App\Models\OrderModel();
        $productModel = new \App\Models\ProductModel();
        $userModel    = new \App\Models\UserModel();

        $deliveredOrdersCount = $orderModel->where('status', 'entregado')->countAllResults();
        $totalOrdersCount     = $orderModel->countAllResults();
        $activeProductsCount  = $productModel->where('status', 1)->countAllResults();
        $totalCustomersCount  = $userModel->where('role', 'customer')->countAllResults();

        $data = [
            'title'                => 'Nosotros | ' . setting('site_name', 'Dulce Gracia'),
            'deliveredOrdersCount' => $deliveredOrdersCount,
            'totalOrdersCount'     => $totalOrdersCount,
            'activeProductsCount'  => $activeProductsCount,
            'totalCustomersCount'  => $totalCustomersCount,
        ];

        return view('home/about', $data);
    }

    public function contact()
    {
        return view('home/contact', ['title' => 'Contacto | ' . setting('site_name', 'Dulce Gracia')]);
    }

    public function sendMessage()
    {
        $rules = [
            'name'    => 'required|min_length[3]',
            'phone'   => 'required|min_length[7]',
            'type'    => 'required|in_list[Sugerencia,Felicitación,Queja,Reclamo]',
            'message' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $messageModel = new \App\Models\MessageModel();
        $messageData = [
            'name'    => trim((string)$this->request->getPost('name')),
            'phone'   => trim((string)$this->request->getPost('phone')),
            'email'   => trim((string)$this->request->getPost('email')),
            'type'    => $this->request->getPost('type'),
            'message' => trim((string)$this->request->getPost('message')),
            'status'  => 'unread'
        ];

        $inserted = $messageModel->insert($messageData);

        if (!$inserted) {
            return redirect()->back()->withInput()->with('error', 'Ocurrió un problema al enviar tu mensaje. Intenta nuevamente.');
        }

        // Crear notificación para administradores
        \App\Models\NotificationModel::createNotification(
            'contact',
            '📩 Nuevo mensaje de ' . $messageData['type'],
            $messageData['name'] . ' envió un mensaje: "' . mb_strimwidth($messageData['message'], 0, 50, '...') . '"',
            base_url('admin/contacto')
        );

        return redirect()->to(base_url('contacto'))->with('success', '¡Gracias por tu mensaje! Lo atenderemos pronto.');
    }
}
