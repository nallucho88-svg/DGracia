<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'type',
        'title',
        'message',
        'target_url',
        'is_read',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Crear notificación de forma rápida
     */
    public static function createNotification(string $type, string $title, string $message, ?string $targetUrl = null): bool
    {
        try {
            $model = new self();
            $model->insert([
                'type'       => $type,
                'title'      => $title,
                'message'    => $message,
                'target_url' => $targetUrl,
                'is_read'    => 0,
            ]);
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Error creando notificación: ' . $e->getMessage());
            return false;
        }
    }
}
