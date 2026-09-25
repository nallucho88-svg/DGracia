<?php

namespace App\Models;

use CodeIgniter\Model;

class CouponModel extends Model
{
    protected $table            = 'coupons';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name', 'description', 'code', 'discount_type', 'value',
        'start_date', 'end_date', 'max_uses', 'used_count',
        'max_uses_per_user', 'category_id', 'product_id',
        'loyal_only', 'is_stackable', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'          => 'required|min_length[3]|max_length[150]',
        'code'          => 'required|alpha_numeric_space|min_length[3]|max_length[50]',
        'discount_type' => 'required|in_list[percentage,fixed]',
        'value'         => 'required|decimal',
        'status'        => 'required|in_list[0,1]'
    ];
}
