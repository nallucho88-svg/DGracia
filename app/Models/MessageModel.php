<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table            = 'messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'phone', 'email', 'type', 'message', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation
    protected $validationRules = [
        'name'    => 'required|max_length[150]',
        'phone'   => 'required|max_length[50]',
        'type'    => 'required|in_list[Sugerencia,Felicitación,Queja,Reclamo]',
        'message' => 'required',
        'status'  => 'required|in_list[unread,read]'
    ];
}
