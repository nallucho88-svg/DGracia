<?php

namespace App\Models;

use CodeIgniter\Model;

class DeliveryZoneModel extends Model
{
    protected $table            = 'delivery_zones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'cost', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'   => 'required|max_length[100]',
        'cost'   => 'required|decimal',
        'status' => 'required|in_list[0,1]'
    ];
}
