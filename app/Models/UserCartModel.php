<?php

namespace App\Models;

use CodeIgniter\Model;

class UserCartModel extends Model
{
    protected $table            = 'user_cart';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'product_id', 'quantity', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';

    /**
     * Sincronizar y fusionar el carrito de sesión con la base de datos para un usuario autenticado.
     */
    public function syncSessionToDb(int $userId, array $sessionCart): array
    {
        $dbItems = $this->where('user_id', $userId)->findAll();
        $dbCartMap = [];
        foreach ($dbItems as $item) {
            $dbCartMap[$item->product_id] = $item->quantity;
        }

        // Fusionar ítems de la sesión en la BD
        foreach ($sessionCart as $item) {
            $prodId = (int) $item['id'];
            $qty    = (int) $item['quantity'];

            if (isset($dbCartMap[$prodId])) {
                $newQty = max($qty, $dbCartMap[$prodId]);
                $this->where('user_id', $userId)->where('product_id', $prodId)->set(['quantity' => $newQty])->update();
                $dbCartMap[$prodId] = $newQty;
            } else {
                $this->insert([
                    'user_id'    => $userId,
                    'product_id' => $prodId,
                    'quantity'   => $qty
                ]);
                $dbCartMap[$prodId] = $qty;
            }
        }

        return $this->loadDbCartToSession($userId);
    }

    /**
     * Cargar carrito desde la base de datos para estructurarlo en la sesión PHP.
     */
    public function loadDbCartToSession(int $userId): array
    {
        $dbItems = $this->where('user_id', $userId)->findAll();
        if (empty($dbItems)) {
            return [];
        }

        $productModel    = new ProductModel();
        $imageModel      = new ProductImageModel();
        $discountService = new \App\Libraries\DiscountService();
        $isLoyal         = (bool) (session()->get('is_loyal') ?? false);

        $cart = [];
        foreach ($dbItems as $item) {
            $product = $productModel->find($item->product_id);
            if (!$product || !$product->availability || $product->status == 0) {
                continue;
            }

            $image = $imageModel->where('product_id', $product->id)->where('is_main', 1)->first();
            $imagePath = $image ? media_url($image->image_path) : media_url('assets/img/placeholder.jpg');

            $discountInfo = $discountService->calculateProductDiscount($product, $isLoyal);
            $unitPrice    = (float) $discountInfo['final_price'];
            $normalPrice  = (float) $discountInfo['original_price'];

            $cart[] = [
                'id'               => $product->id,
                'name'             => $product->name,
                'price'            => $unitPrice,
                'normal_price'     => $normalPrice,
                'offer_price'      => $discountInfo['has_discount'] ? $unitPrice : null,
                'discount_amount'  => (float) $discountInfo['discount_amount'],
                'has_discount'     => (bool) $discountInfo['has_discount'],
                'discount_percent' => $discountInfo['discount_percentage'],
                'quantity'         => (int) $item->quantity,
                'subtotal'         => $unitPrice * (int) $item->quantity,
                'image'            => $imagePath
            ];
        }

        return $cart;
    }

    /**
     * Guardar estado completo del carrito desde sesión hacia BD.
     */
    public function saveSessionCart(int $userId, array $sessionCart): void
    {
        $this->where('user_id', $userId)->delete();
        foreach ($sessionCart as $item) {
            if ((int)$item['quantity'] > 0) {
                $this->insert([
                    'user_id'    => $userId,
                    'product_id' => (int) $item['id'],
                    'quantity'   => (int) $item['quantity']
                ]);
            }
        }
    }

    /**
     * Vaciar carrito del usuario en la BD.
     */
    public function clearUserCart(int $userId): void
    {
        $this->where('user_id', $userId)->delete();
    }
}
