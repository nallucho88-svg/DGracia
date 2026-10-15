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
        }
    }

    /**
     * Verificar y generar notificaciones para ofertas próximas a vencer (FASE 7)
     */
    public static function checkExpiringOffers(): void
    {
        try {
            $db = \Config\Database::connect();
            $now = date('Y-m-d H:i:s');
            $inThreeDays = date('Y-m-d H:i:s', strtotime('+3 days'));
            $todayDate = date('Y-m-d');

            // Consultar ofertas activas que vencen en los próximos 3 días
            $expiringDiscounts = $db->table('discounts')
                ->where('status', 1)
                ->where('end_date >=', $now)
                ->where('end_date <=', $inThreeDays)
                ->get()
                ->getResult();

            $notifModel = new self();

            foreach ($expiringDiscounts as $disc) {
                // Control anti-duplicados: verificar si ya se notificó esta oferta el día de hoy
                $existing = $notifModel->where('type', 'offer_expiring')
                    ->like('title', $disc->title)
                    ->where('DATE(created_at)', $todayDate)
                    ->first();

                if (!$existing) {
                    $formattedEndDate = date('d/m/Y', strtotime($disc->end_date));
                    self::createNotification(
                        'offer_expiring',
                        '⏰ Oferta próxima a vencer: ' . $disc->title,
                        'La oferta "' . $disc->title . '" vence pronto el ' . $formattedEndDate . '.',
                        base_url('admin/descuentos')
                    );
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error verificando ofertas por vencer: ' . $e->getMessage());
        }
    }
}
