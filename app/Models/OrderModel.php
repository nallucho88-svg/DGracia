<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_number', 'user_id', 'customer_name', 'phone', 'whatsapp', 'customer_email',
        'address', 'neighborhood', 'city', 'delivery_instructions', 
        'delivery_date', 'delivery_time', 'delivery_type', 
        'delivery_zone_id', 'delivery_cost', 'coupon_code', 'coupon_discount',
        'discount_amount', 'subtotal', 'total', 
        'payment_method', 'payment_status', 'status', 'source', 'notes'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'order_number'   => 'required|is_unique[orders.order_number]',
        'customer_name'  => 'required|max_length[150]',
        'phone'          => 'required|max_length[50]',
        'delivery_type'  => 'required|in_list[domicilio,recoger]',
        'payment_method' => 'required|max_length[50]',
        'status'         => 'in_list[pendiente,en preparacion,enviado,entregado,cancelado]',
    ];
}
