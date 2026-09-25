<?php

namespace App\Models;

use CodeIgniter\Model;

class PointTransactionModel extends Model
{
    protected $table            = 'point_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'user_id',
        'order_id',
        'points',
        'type',
        'description',
        'created_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';
}
