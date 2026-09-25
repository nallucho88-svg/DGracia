<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_id', 'product_id', 'product_name', 'sku', 'original_price', 'offer_price', 'discount_amount', 'price', 'quantity', 'subtotal'
    ];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules = [
        'order_id'     => 'required|is_natural_no_zero',
        'product_name' => 'required|max_length[150]',
        'price'        => 'required|decimal',
        'quantity'     => 'required|is_natural_no_zero',
        'subtotal'     => 'required|decimal'
    ];
}
