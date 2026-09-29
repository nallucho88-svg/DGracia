<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\FavoriteModel;
use App\Models\ProductModel;
use App\Models\ProductImageModel;
use App\Models\PointTransactionModel;
use App\Models\PointGoalModel;

class CustomerAccount extends BaseController
{
    protected $userId;

    public function __construct()
    {
        $this->userId = session()->get('user_id');
    }

    /**
     * Dashboard Principal del Cliente
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $orderModel = new OrderModel();
        $favoriteModel = new FavoriteModel();
        $pointGoalModel = new PointGoalModel();

        $user = $userModel->find($userId);

        // Estadísticas
        $ordersCount = $orderModel->where('user_id', $userId)->countAllResults();
        $favoritesCount = $favoriteModel->where('user_id', $userId)->countAllResults();
        $userPoints = (int) ($user->points ?? 0);

        // Próxima Meta de Puntos
        $nextGoal = $pointGoalModel->where('status', 1)
            ->where('points_required >', $userPoints)
            ->orderBy('points_required', 'ASC')
            ->first();

        if (!$nextGoal) {
            $nextGoal = $pointGoalModel->where('status', 1)->orderBy('points_required', 'DESC')->first();
        }

        $goalProgress = 100;
        if ($nextGoal && $nextGoal->points_required > 0) {
            $goalProgress = min(100, round(($userPoints / $nextGoal->points_required) * 100));
        }

        // Últimos 5 pedidos
        $recentOrders = $orderModel->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        $data = [
            'title'          => 'Mi Cuenta | Dulce Gracia',
            'user'           => $user,
            'ordersCount'    => $ordersCount,
            'favoritesCount' => $favoritesCount,
            'userPoints'     => $userPoints,
            'nextGoal'       => $nextGoal,
            'goalProgress'   => $goalProgress,
            'recentOrders'   => $recentOrders,
            'activeTab'      => 'dashboard'
        ];

        return view('customer/dashboard', $data);
    }

    /**
     * Ver/Editar Perfil
     */
    public function profile()
    {
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        $data = [
            'title'     => 'Mi Perfil | Dulce Gracia',
            'user'      => $user,
            'activeTab' => 'profile'
        ];

        return view('customer/profile', $data);
    }

    /**
     * Actualizar Información Personal
     */
    public function updateProfile()
    {
        $userId = session()->get('user_id');
        $rules = [
            'name'     => 'required|min_length[2]|max_length[100]',
            'lastname' => 'required|min_length[2]|max_length[100]',
            'phone'    => 'required|min_length[7]',
            'whatsapp' => 'permit_empty|min_length[7]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $updateData = [
            'name'     => trim($this->request->getPost('name')),
            'lastname' => trim($this->request->getPost('lastname')),
            'phone'    => trim($this->request->getPost('phone')),
            'whatsapp' => trim($this->request->getPost('whatsapp')),
        ];

        $userModel->update($userId, $updateData);

        // Actualizar datos de sesión
        session()->set([
            'name'     => $updateData['name'],
            'lastname' => $updateData['lastname'],
            'phone'    => $updateData['phone'],
            'whatsapp' => $updateData['whatsapp']
        ]);

        return redirect()->to(base_url('mi-cuenta/perfil'))->with('success', 'Tu información personal ha sido actualizada correctamente.');
    }

    /**
     * Cambiar Contraseña
     */
    public function updatePassword()
    {
        $userId = session()->get('user_id');
        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newPassword = $this->request->getPost('new_password');
        $pwdCheck = \App\Controllers\PasswordReset::validatePassword($newPassword);
        if (!$pwdCheck['valid']) {
            return redirect()->back()->withInput()->with('error', $pwdCheck['message']);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!password_verify($this->request->getPost('current_password'), $user->password)) {
            return redirect()->back()->with('error', 'La contraseña actual ingresada es incorrecta.');
        }

        $userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        return redirect()->to(base_url('mi-cuenta/perfil'))->with('success', 'Tu contraseña se ha cambiado exitosamente.');
    }

    /**
     * Mis Pedidos
     */
    public function orders()
    {
        $userId = session()->get('user_id');
        $orderModel = new OrderModel();

        $orders = $orderModel->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'title'     => 'Mis Pedidos | Dulce Gracia',
            'orders'    => $orders,
            'activeTab' => 'orders'
        ];

        return view('customer/orders', $data);
    }

    /**
     * Detalle y Seguimiento de Pedido con Timeline Visual
     */
    public function orderDetail($orderNumber)
    {
        $userId = session()->get('user_id');
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();

        $order = $orderModel->where('order_number', $orderNumber)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            return redirect()->to(base_url('mi-cuenta/pedidos'))->with('error', 'Pedido no encontrado.');
        }

        $items = $orderItemModel->where('order_id', $order->id)->findAll();
        $ratingModel = new \App\Models\RatingModel();

        foreach ($items as &$item) {
            $item->rating = $ratingModel->where('order_item_id', $item->id)->first();
        }

        // Mapeo de timeline de estados
        $statuses = [
            'pendiente'     => ['label' => 'Pedido Recibido', 'icon' => 'fa-clipboard-check', 'step' => 1],
            'en preparacion'=> ['label' => 'En Preparación', 'icon' => 'fa-blender', 'step' => 2],
            'enviado'       => ['label' => 'En Camino', 'icon' => 'fa-motorcycle', 'step' => 3],
            'entregado'     => ['label' => 'Entregado', 'icon' => 'fa-check-circle', 'step' => 4],
            'cancelado'     => ['label' => 'Cancelado', 'icon' => 'fa-times-circle', 'step' => 0]
        ];

        $currentStep = $statuses[$order->status]['step'] ?? 1;

        $data = [
            'title'       => 'Seguimiento de Pedido #' . $order->order_number,
            'order'       => $order,
            'items'       => $items,
            'statuses'    => $statuses,
            'currentStep' => $currentStep,
            'activeTab'   => 'orders'
        ];

        return view('customer/order_detail', $data);
    }

    /**
     * Guardar Valoración de Producto (1 por compra)
     */
    public function saveRating()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Debes iniciar sesión para calificar.']);
            }
            return redirect()->to(base_url('login'))->with('error', 'Debes iniciar sesión para calificar.');
        }

        $orderItemId = (int) $this->request->getPost('order_item_id');
        $ratingVal   = (int) $this->request->getPost('rating');
        $comment     = trim((string) $this->request->getPost('comment'));

        if ($ratingVal < 1 || $ratingVal > 5) {
            $msg = 'La valoración debe ser entre 1 y 5 estrellas.';
            return $this->request->isAJAX() 
                ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) 
                : redirect()->back()->with('error', $msg);
        }

        $orderItemModel = new \App\Models\OrderItemModel();
        $orderModel     = new \App\Models\OrderModel();
        $ratingModel    = new \App\Models\RatingModel();

        $item = $orderItemModel->find($orderItemId);
        if (!$item) {
            $msg = 'Item de pedido no encontrado.';
            return $this->request->isAJAX() 
                ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) 
                : redirect()->back()->with('error', $msg);
        }

        $order = $orderModel->find($item->order_id);
        if (!$order || (int)$order->user_id !== (int)$userId) {
            $msg = 'No tienes permiso para calificar esta compra.';
            return $this->request->isAJAX() 
                ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) 
                : redirect()->back()->with('error', $msg);
        }

        // Verificar si ya tiene valoración para esta compra específica
        $existing = $ratingModel->where('order_item_id', $orderItemId)->first();
        if ($existing) {
            $msg = 'Ya calificaste este producto para esta compra.';
            return $this->request->isAJAX() 
                ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) 
                : redirect()->back()->with('error', $msg);
        }

        // Insertar valoración
        $ratingModel->insert([
            'user_id'       => $userId,
            'order_id'      => $order->id,
            'order_item_id' => $item->id,
            'product_id'    => $item->product_id,
            'rating'        => $ratingVal,
            'comment'       => esc(strip_tags($comment)),
            'status'        => 'visible'
        ]);

        $successMsg = '¡Muchas gracias por calificar tu producto!';

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $successMsg,
                'rating'  => $ratingVal
            ]);
        }

        return redirect()->back()->with('success', $successMsg);
    }

    /**
     * Billetera de Puntos
     */
    public function points()
    {
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $txModel   = new PointTransactionModel();
        $goalModel = new PointGoalModel();

        $user = $userModel->find($userId);
        $points = (int) ($user->points ?? 0);

        // Configuración de valor de punto
        $pointValueCop = (float) setting('point_monetary_value', '500'); // Por defecto 1 punto = $50 COP, ej: 100 ptos = $5.000 COP
        $monetaryEquivalent = $points * $pointValueCop;

        // Historial de movimientos
        $transactions = $txModel->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Totales ganados y gastados
        $earned = 0;
        $spent  = 0;
        foreach ($transactions as $tx) {
            if ($tx->points > 0) {
                $earned += $tx->points;
            } else {
                $spent += abs($tx->points);
            }
        }

        // Metas activas
        $goals = $goalModel->where('status', 1)->orderBy('points_required', 'ASC')->findAll();

        $data = [
            'title'              => 'Mis Puntos | Dulce Gracia',
            'points'             => $points,
            'monetaryEquivalent' => $monetaryEquivalent,
            'transactions'       => $transactions,
            'earned'             => $earned,
            'spent'              => $spent,
            'goals'              => $goals,
            'activeTab'          => 'points'
        ];

        return view('customer/points', $data);
    }

    /**
     * Metas de Puntos
     */
    public function goals()
    {
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $goalModel = new PointGoalModel();

        $user = $userModel->find($userId);
        $points = (int) ($user->points ?? 0);

        $goals = $goalModel->where('status', 1)->orderBy('points_required', 'ASC')->findAll();

        $data = [
            'title'     => 'Mis Metas | Dulce Gracia',
            'points'    => $points,
            'goals'     => $goals,
            'activeTab' => 'goals'
        ];

        return view('customer/goals', $data);
    }

    /**
     * Mis Favoritos
     */
    public function favorites()
    {
        $userId = session()->get('user_id');
        $favoriteModel = new FavoriteModel();
        $productModel  = new ProductModel();
        $imageModel    = new ProductImageModel();
        $discountService = new \App\Libraries\DiscountService();
        $isLoyal = (bool) (session()->get('is_loyal') ?? false);

        $favs = $favoriteModel->where('user_id', $userId)->findAll();
        $products = [];

        foreach ($favs as $f) {
            $p = $productModel->where('id', $f->product_id)->where('status', 1)->first();
            if ($p) {
                $img = $imageModel->where('product_id', $p->id)->where('is_main', 1)->first();
                $p->main_image = media_url($img ? $img->image_path : '', 'assets/img/placeholder.jpg');
                $p->discount_info = $discountService->calculateProductDiscount($p, $isLoyal);
                $products[] = $p;
            }
        }

        $data = [
            'title'     => 'Mis Favoritos | Dulce Gracia',
            'products'  => $products,
            'activeTab' => 'favorites'
        ];

        return view('customer/favorites', $data);
    }

    /**
     * Agregar / Eliminar de Favoritos por AJAX
     */
    public function toggleFavorite()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Petición inválida']);
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'unauthorized', 'message' => 'Debes iniciar sesión para guardar favoritos.']);
        }

        $productId = (int) $this->request->getPost('product_id');
        $favoriteModel = new FavoriteModel();

        $existing = $favoriteModel->where('user_id', $userId)->where('product_id', $productId)->first();

        if ($existing) {
            $favoriteModel->delete($existing->id);
            $isFavorite = false;
            $msg = 'Producto removido de tus favoritos.';
        } else {
            $favoriteModel->insert([
                'user_id'    => $userId,
                'product_id' => $productId
            ]);
            $isFavorite = true;
            $msg = '¡Producto agregado a tus favoritos!';
        }

        $count = $favoriteModel->where('user_id', $userId)->countAllResults();

        return $this->response->setJSON([
            'status'      => 'success',
            'is_favorite' => $isFavorite,
            'message'     => $msg,
            'fav_count'   => $count
        ]);
    }
}
