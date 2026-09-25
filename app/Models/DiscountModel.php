<?php

namespace App\Models;

use CodeIgniter\Model;

class DiscountModel extends Model
{
    protected $table            = 'discounts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title', 'type', 'discount_type', 'value', 
        'product_id', 'category_id', 'start_date', 
        'end_date', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'title'         => 'required|min_length[3]|max_length[150]',
        'type'          => 'required|in_list[product,category,loyalty,general]',
        'discount_type' => 'required|in_list[percentage,fixed]',
        'value'         => 'required|decimal',
        'status'        => 'required|in_list[0,1]'
    ];
}
