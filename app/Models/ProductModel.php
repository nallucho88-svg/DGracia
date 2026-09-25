<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id', 'name', 'slug', 'short_description', 
        'description', 'price', 'cost', 'ingredients', 'portions', 
        'availability', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'category_id'       => 'required|is_natural_no_zero',
        'name'              => 'required|min_length[3]|max_length[150]',
        'slug'              => 'required|alpha_dash|is_unique[products.slug,id,{id}]',
        'price'             => 'required|decimal',
        'availability'      => 'required|in_list[0,1]',
        'status'            => 'required|in_list[0,1]'
    ];
}
