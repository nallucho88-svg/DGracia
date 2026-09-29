<?php

namespace App\Libraries;

use Config\Services;

class EmailService
{
    /**
     * Enviar correo de confirmación de compra a los administradores.
     */
    public static function sendOrderNotificationToAdmins(object $order, array $items): bool
    {
        try {
            // Correos de destino desde .env o configuración
            $adminEmail = env('ADMIN_EMAIL', setting('contact_email', 'admin@dulcegracia.com'));
            $wifeEmail  = env('WIFE_EMAIL', setting('admin_secondary_email', ''));

            $recipients = array_filter([$adminEmail, $wifeEmail]);

            if (empty($recipients)) {
                return false;
            }

            $subject = '🛍️ Nuevo Pedido Confirmado #' . $order->order_number . ' - Dulce Gracia';

            $itemsHtml = '';
            foreach ($items as $it) {
                $normalPriceFmt = isset($it->original_price) && $it->original_price > 0 ? '$' . number_format($it->original_price, 0, ',', '.') : '$' . number_format($it->price, 0, ',', '.');
                $priceFmt       = '$' . number_format($it->price, 0, ',', '.');
                $subtotalFmt    = '$' . number_format($it->subtotal, 0, ',', '.');

                $itemsHtml .= "
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>" . esc($it->product_name) . "</strong><br><small style='color:#777;'>SKU: " . esc($it->sku ?? 'N/A') . "</small></td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; text-align: center;'>" . (int)$it->quantity . "</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; text-align: right;'>{$normalPriceFmt}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; text-align: right; color:#198754;'><strong>{$priceFmt}</strong></td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; text-align: right;'><strong>{$subtotalFmt}</strong></td>
                </tr>";
            }

            $couponInfo = !empty($order->coupon_code) ? esc($order->coupon_code) . ' (-$' . number_format($order->coupon_discount, 0, ',', '.') . ')' : 'No aplica';
            $shippingFmt = '$' . number_format($order->delivery_cost, 0, ',', '.');
            $subtotalFmt = '$' . number_format($order->subtotal, 0, ',', '.');
            $totalFmt    = '$' . number_format($order->total, 0, ',', '.');

            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden;'>
                <div style='background-color: #d4a373; padding: 20px; text-align: center; color: white;'>
                    <h1 style='margin: 0; font-size: 24px;'>🎉 ¡Nuevo Pedido Recibido!</h1>
                    <p style='margin: 5px 0 0 0;'>Dulce Gracia - Repostería</p>
                </div>
                <div style='padding: 20px;'>
                    <h3 style='color: #d4a373; border-bottom: 2px solid #f4a261; padding-bottom: 5px;'>DATOS DEL PEDIDO</h3>
                    <p><strong>Número de Pedido:</strong> {$order->order_number}</p>
                    <p><strong>Fecha y Hora:</strong> " . date('d/m/Y h:i A', strtotime($order->created_at)) . "</p>
                    <p><strong>Método de Pago:</strong> " . esc($order->payment_method) . " (Estado: " . ucfirst($order->payment_status ?? 'pendiente') . ")</p>
                    <p><strong>Tipo de Entrega:</strong> " . ucfirst($order->delivery_type) . "</p>

                    <h3 style='color: #d4a373; border-bottom: 2px solid #f4a261; padding-bottom: 5px;'>DATOS DEL CLIENTE</h3>
                    <p><strong>Cliente:</strong> " . esc($order->customer_name) . "</p>
                    <p><strong>Teléfono:</strong> " . esc($order->phone) . "</p>
                    <p><strong>WhatsApp:</strong> " . esc($order->whatsapp ?: 'N/A') . "</p>
                    <p><strong>Correo Electrónico:</strong> " . esc($order->customer_email ?: 'No proporcionado') . "</p>
                    <p><strong>Dirección:</strong> " . esc($order->address ?: 'Recoger en tienda') . " (" . esc($order->city ?: 'Medellín') . ")</p>
                    " . (!empty($order->notes) ? "<p><strong>Notas:</strong> " . esc($order->notes) . "</p>" : "") . "

                    <h3 style='color: #d4a373; border-bottom: 2px solid #f4a261; padding-bottom: 5px;'>PRODUCTOS SOLICITADOS</h3>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <thead>
                            <tr style='background-color: #f8f9fa;'>
                                <th style='padding: 8px; text-align: left;'>Producto</th>
                                <th style='padding: 8px; text-align: center;'>Cant</th>
                                <th style='padding: 8px; text-align: right;'>P. Normal</th>
                                <th style='padding: 8px; text-align: right;'>P. Oferta</th>
                                <th style='padding: 8px; text-align: right;'>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$itemsHtml}
                        </tbody>
                    </table>

                    <div style='margin-top: 20px; text-align: right; background-color: #f8f9fa; padding: 15px; border-radius: 8px;'>
                        <p style='margin: 3px 0;'><strong>Subtotal:</strong> {$subtotalFmt}</p>
                        <p style='margin: 3px 0;'><strong>Cupón Aplicado:</strong> {$couponInfo}</p>
                        <p style='margin: 3px 0;'><strong>Costo Domicilio:</strong> {$shippingFmt}</p>
                        <h2 style='margin: 10px 0 0 0; color: #d4a373;'>TOTAL: {$totalFmt}</h2>
                    </div>
                </div>
                <div style='background-color: #f8f9fa; text-align: center; padding: 10px; font-size: 12px; color: #777;'>
                    Notificación automática de compras - Dulce Gracia © " . date('Y') . "
                </div>
            </div>";

            $email = self::getEmailClient();
            $email->setTo(implode(',', $recipients));
            $fromEmail = setting('smtp_from_email', setting('contact_email', 'notificaciones@dulcegracia.com'));
            $fromName  = setting('smtp_from_name', setting('site_name', 'Dulce Gracia'));
            $email->setFrom($fromEmail, $fromName);
            $email->setSubject($subject);
            $email->setMessage($html);
            $email->setMailType('html');

            return $email->send();
        } catch (\Throwable $e) {
            log_message('error', 'Error enviando correo de pedido: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar correo con enlace para restablecer la contraseña.
     */
    public static function sendPasswordResetEmail(string $recipientEmail, string $resetToken): bool
    {
        try {
            $resetLink = base_url('restablecer-contrasena/' . $resetToken);
            $subject   = '🔑 Restablecer tu contraseña - Dulce Gracia';

            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 550px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden;'>
                <div style='background-color: #d4a373; padding: 20px; text-align: center; color: white;'>
                    <h2 style='margin: 0;'>Dulce Gracia</h2>
                    <p style='margin: 5px 0 0 0;'>Solicitud de Restablecimiento de Contraseña</p>
                </div>
                <div style='padding: 25px;'>
                    <p>Hola,</p>
                    <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en Dulce Gracia.</p>
                    <p>Haz clic en el siguiente botón para crear una nueva contraseña. Este enlace expira en 60 minutos:</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$resetLink}' style='background-color: #d4a373; color: white; text-decoration: none; padding: 12px 25px; border-radius: 25px; font-weight: bold; font-size: 16px; display: inline-block;'>
                            Restablecer mi Contraseña
                        </a>
                    </div>

                    <p style='font-size: 13px; color: #777;'>Si no solicitaste este cambio, puedes ignorar este mensaje con total seguridad. Tu contraseña actual no cambiará.</p>
                </div>
                <div style='background-color: #f8f9fa; text-align: center; padding: 12px; font-size: 12px; color: #888;'>
                    Dulce Gracia © " . date('Y') . " - Seguridad
                </div>
            </div>";

            $email = self::getEmailClient();
            $email->setTo($recipientEmail);
            $fromEmail = setting('smtp_from_email', setting('contact_email', 'soporte@dulcegracia.com'));
            $fromName  = setting('smtp_from_name', setting('site_name', 'Dulce Gracia'));
            $email->setFrom($fromEmail, $fromName);
            $email->setSubject($subject);
            $email->setMessage($html);
            $email->setMailType('html');

            return $email->send();
        } catch (\Throwable $e) {
            log_message('error', 'Error enviando correo de recuperación: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cliente de Email con configuración SMTP centralizada
     */
    public static function getEmailClient()
    {
        $config = [
            'mailType' => 'html',
            'charset'  => 'utf-8',
            'CRLF'     => "\r\n",
            'newline'  => "\r\n"
        ];

        $host   = setting('smtp_host', '');
        $user   = setting('smtp_user', '');
        $pass   = setting('smtp_pass', '');
        $port   = setting('smtp_port', '');
        $crypto = setting('smtp_crypto', '');

        if (!empty($host) && !empty($user)) {
            $config['protocol']   = 'smtp';
            $config['SMTPHost']   = $host;
            $config['SMTPUser']   = $user;
            $config['SMTPPass']   = $pass;
            $config['SMTPPort']   = (int) ($port ?: 587);
            $config['SMTPCrypto'] = ($crypto === 'none') ? '' : ($crypto ?: 'tls');
        }

        $email = Services::email($config);
        $email->clear(true);
        return $email;
    }
}
