<?php

namespace App\Models;

use CodeIgniter\Model;

class RatingModel extends Model
{
    protected $table            = 'ratings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'order_id',
        'order_item_id',
        'product_id',
        'rating',
        'comment',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Obtener estadísticas de estrellas para un producto
     */
    public function getProductRatingSummary(int $productId): array
    {
        $db = \Config\Database::connect();
        
        $stats = $db->query("
            SELECT 
                COUNT(*) AS total_reviews,
                COALESCE(AVG(rating), 0) AS avg_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS count_5,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS count_4,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS count_3,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS count_2,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS count_1
            FROM ratings
            WHERE product_id = ? AND status = 'visible'
        ", [$productId])->getRowArray();

        return [
            'total'        => (int) ($stats['total_reviews'] ?? 0),
            'average'      => round((float) ($stats['avg_rating'] ?? 0), 1),
            'distribution' => [
                5 => (int) ($stats['count_5'] ?? 0),
                4 => (int) ($stats['count_4'] ?? 0),
                3 => (int) ($stats['count_3'] ?? 0),
                2 => (int) ($stats['count_2'] ?? 0),
                1 => (int) ($stats['count_1'] ?? 0),
            ]
        ];
    }
}
