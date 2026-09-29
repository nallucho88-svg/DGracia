<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RatingModel;
use CodeIgniter\Database\BaseConnection;

class Ratings extends BaseController
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $productFilter = trim((string)$this->request->getGet('product_id'));
        $ratingFilter  = trim((string)$this->request->getGet('rating'));
        $statusFilter  = trim((string)$this->request->getGet('status'));
        $orderFilter   = trim((string)$this->request->getGet('order_number'));
        $searchFilter  = trim((string)$this->request->getGet('search'));

        $builder = $this->db->table('ratings r')
            ->select('r.*, 
                     u.name as customer_name, u.email as customer_email, 
                     o.order_number, 
                     p.name as product_name,
                     (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_main DESC, pi.id ASC LIMIT 1) AS product_image')
            ->join('users u', 'u.id = r.user_id', 'left')
            ->join('orders o', 'o.id = r.order_id', 'left')
            ->join('products p', 'p.id = r.product_id', 'left');

        if ($productFilter !== '') {
            $builder->where('r.product_id', (int)$productFilter);
        }
        if ($ratingFilter !== '') {
            $builder->where('r.rating', (int)$ratingFilter);
        }
        if ($statusFilter !== '') {
            $builder->where('r.status', $statusFilter);
        } else {
            $builder->where('r.status !=', 'eliminada');
        }
        if ($orderFilter !== '') {
            $builder->like('o.order_number', $orderFilter);
        }
        if ($searchFilter !== '') {
            $builder->groupStart()
                ->like('u.name', $searchFilter)
                ->orLike('u.email', $searchFilter)
                ->orLike('p.name', $searchFilter)
                ->orLike('r.comment', $searchFilter)
                ->groupEnd();
        }

        $ratings = $builder->orderBy('r.created_at', 'DESC')->get()->getResultArray();

        $products = $this->db->table('products')->select('id, name')->orderBy('name', 'ASC')->get()->getResultArray();

        $data = [
            'title'          => 'Gestión de Valoraciones | Admin',
            'ratings'        => $ratings,
            'products'       => $products,
            'productFilter'  => $productFilter,
            'ratingFilter'   => $ratingFilter,
            'statusFilter'   => $statusFilter,
            'orderFilter'    => $orderFilter,
            'searchFilter'   => $searchFilter,
        ];

        return view('admin/ratings/index', $data);
    }

    public function changeStatus($id, $newStatus)
    {
        $ratingModel = new RatingModel();
        $rating = $ratingModel->find($id);

        if (!$rating) {
            return redirect()->to(base_url('admin/valoraciones'))->with('error', 'Valoración no encontrada.');
        }

        if (!in_array($newStatus, ['visible', 'oculta', 'eliminada'])) {
            return redirect()->to(base_url('admin/valoraciones'))->with('error', 'Estado no válido.');
        }

        $ratingModel->update($id, ['status' => $newStatus]);

        $msg = ($newStatus === 'visible') 
            ? 'Valoración visible en la tienda.' 
            : (($newStatus === 'oculta') ? 'Valoración ocultada correctamente.' : 'Valoración eliminada.');

        return redirect()->to(base_url('admin/valoraciones'))->with('success', $msg);
    }

    public function delete($id)
    {
        return $this->changeStatus($id, 'eliminada');
    }
}
