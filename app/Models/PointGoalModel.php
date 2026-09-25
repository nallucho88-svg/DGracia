<?php

namespace App\Models;

use CodeIgniter\Model;

class PointGoalModel extends Model
{
    protected $table            = 'point_goals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';

    protected $allowedFields    = [
        'title',
        'points_required',
        'reward_type',
        'reward_value',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
}
