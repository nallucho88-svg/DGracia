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
            // Correos de destino desde configuración de tienda, .env o contacto
            $adminNotifyEmail = setting('admin_notification_email', '');
            $adminEmail       = !empty($adminNotifyEmail) ? $adminNotifyEmail : env('ADMIN_EMAIL', setting('contact_email', 'admin@dulcegracia.com'));
            $wifeEmail        = env('WIFE_EMAIL', setting('admin_secondary_email', ''));

            $recipients = array_filter([$adminEmail, $wifeEmail]);

            if (empty($recipients)) {
                return false;
            }

            $subject = '🛍️ Nueva compra recibida — Pedido #' . $order->order_number;
            $adminViewLink = base_url('admin/pedidos/ver/' . $order->id);

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

            $couponInfo  = !empty($order->coupon_code) ? esc($order->coupon_code) . ' (-$' . number_format($order->coupon_discount, 0, ',', '.') . ')' : 'No aplica';
            $shippingFmt = '$' . number_format($order->delivery_cost, 0, ',', '.');
            $subtotalFmt = '$' . number_format($order->subtotal, 0, ',', '.');
            $totalFmt    = '$' . number_format($order->total, 0, ',', '.');

            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden;'>
                <div style='background-color: #d4a373; padding: 20px; text-align: center; color: white;'>
                    <h1 style='margin: 0; font-size: 24px;'>🎉 ¡Nueva Compra Recibida!</h1>
                    <p style='margin: 5px 0 0 0;'>Dulce Gracia - Repostería</p>
                </div>
                <div style='padding: 20px;'>
                    <h3 style='color: #d4a373; border-bottom: 2px solid #f4a261; padding-bottom: 5px;'>DATOS DEL PEDIDO</h3>
                    <p><strong>Número de Pedido:</strong> {$order->order_number}</p>
                    <p><strong>Fecha y Hora:</strong> " . date('d/m/Y h:i A', strtotime($order->created_at ?? date('Y-m-d H:i:s'))) . "</p>
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

                    <div style='text-align: center; margin-top: 25px;'>
                        <a href='{$adminViewLink}' style='background-color: #d4a373; color: white; text-decoration: none; padding: 12px 25px; border-radius: 25px; font-weight: bold; font-size: 15px; display: inline-block;'>
                            Gestionar Pedido en el Admin
                        </a>
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
            log_message('error', 'Error enviando correo de pedido a admin: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar correo de confirmación de compra al cliente.
     */
    public static function sendOrderConfirmationToCustomer(object $order, array $items): bool
    {
        try {
            $customerEmail = trim((string)($order->customer_email ?? ''));
            if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                return false;
            }

            $subject = '🍰 ¡Gracias por tu compra en Dulce Gracia! Pedido #' . $order->order_number;
            $trackingLink = base_url('pedido/confirmacion/' . $order->order_number);

            $itemsHtml = '';
            foreach ($items as $it) {
                $normalPriceFmt = isset($it->original_price) && $it->original_price > 0 ? '$' . number_format($it->original_price, 0, ',', '.') : '$' . number_format($it->price, 0, ',', '.');
                $priceFmt       = '$' . number_format($it->price, 0, ',', '.');
                $subtotalFmt    = '$' . number_format($it->subtotal, 0, ',', '.');

                $itemsHtml .= "
                <tr>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>" . esc($it->product_name) . "</strong></td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>" . (int)$it->quantity . "</td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>{$normalPriceFmt}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right; color:#198754;'><strong>{$priceFmt}</strong></td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'><strong>{$subtotalFmt}</strong></td>
                </tr>";
            }

            $couponInfo  = !empty($order->coupon_code) ? esc($order->coupon_code) . ' (-$' . number_format($order->coupon_discount, 0, ',', '.') . ')' : 'No aplica';
            $shippingFmt = '$' . number_format($order->delivery_cost, 0, ',', '.');
            $subtotalFmt = '$' . number_format($order->subtotal, 0, ',', '.');
            $totalFmt    = '$' . number_format($order->total, 0, ',', '.');
            $storePhone  = setting('contact_phone', '3108690810');

            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden;'>
                <div style='background-color: #d4a373; padding: 25px; text-align: center; color: white;'>
                    <h1 style='margin: 0; font-size: 24px;'>🎉 ¡Gracias por tu compra!</h1>
                    <p style='margin: 5px 0 0 0;'>Hemos recibido tu pedido en Dulce Gracia</p>
                </div>
                <div style='padding: 25px;'>
                    <p>Hola <strong>" . esc($order->customer_name) . "</strong>,</p>
                    <p>Tu pedido ha sido registrado con éxito. A continuación encontrarás el resumen detallado de tu compra:</p>

                    <div style='background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                        <p style='margin: 4px 0;'><strong>Pedido #:</strong> {$order->order_number}</p>
                        <p style='margin: 4px 0;'><strong>Fecha y Hora:</strong> " . date('d/m/Y h:i A', strtotime($order->created_at ?? date('Y-m-d H:i:s'))) . "</p>
                        <p style='margin: 4px 0;'><strong>Método de Pago:</strong> " . esc($order->payment_method) . "</p>
                        <p style='margin: 4px 0;'><strong>Tipo de Entrega:</strong> " . ucfirst($order->delivery_type) . "</p>
                        <p style='margin: 4px 0;'><strong>Dirección:</strong> " . esc($order->address ?: 'Recoger en tienda') . " (" . esc($order->city ?: 'Medellín') . ")</p>
                    </div>

                    <h3 style='color: #d4a373; border-bottom: 2px solid #f4a261; padding-bottom: 5px;'>PRODUCTOS COMPRADOS</h3>
                    <table style='width: 100%; border-collapse: collapse; margin-bottom: 15px;'>
                        <thead>
                            <tr style='background-color: #f8f9fa;'>
                                <th style='padding: 10px; text-align: left;'>Producto</th>
                                <th style='padding: 10px; text-align: center;'>Cant</th>
                                <th style='padding: 10px; text-align: right;'>P. Normal</th>
                                <th style='padding: 10px; text-align: right;'>P. Oferta</th>
                                <th style='padding: 10px; text-align: right;'>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$itemsHtml}
                        </tbody>
                    </table>

                    <div style='text-align: right; background-color: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;'>
                        <p style='margin: 3px 0;'><strong>Subtotal:</strong> {$subtotalFmt}</p>
                        <p style='margin: 3px 0;'><strong>Cupón Aplicado:</strong> {$couponInfo}</p>
                        <p style='margin: 3px 0;'><strong>Costo Domicilio:</strong> {$shippingFmt}</p>
                        <h2 style='margin: 10px 0 0 0; color: #d4a373;'>TOTAL CLIENTE: {$totalFmt}</h2>
                    </div>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$trackingLink}' style='background-color: #d4a373; color: white; text-decoration: none; padding: 14px 28px; border-radius: 25px; font-weight: bold; font-size: 16px; display: inline-block; box-shadow: 0 4px 10px rgba(212,163,115,0.4);'>
                            📍 Ver seguimiento de mi pedido
                        </a>
                    </div>

                    <p style='font-size: 13px; color: #666; text-align: center;'>Si tienes preguntas sobre tu pedido, comunícate con nosotros al WhatsApp/Teléfono: <strong>{$storePhone}</strong>.</p>
                </div>
                <div style='background-color: #f8f9fa; text-align: center; padding: 12px; font-size: 12px; color: #888;'>
                    Dulce Gracia © " . date('Y') . " - Endulzamos tus momentos especiales
                </div>
            </div>";

            $email = self::getEmailClient();
            $email->setTo($customerEmail);
            $fromEmail = setting('smtp_from_email', setting('contact_email', 'notificaciones@dulcegracia.com'));
            $fromName  = setting('smtp_from_name', setting('site_name', 'Dulce Gracia'));
            $email->setFrom($fromEmail, $fromName);
            $email->setSubject($subject);
            $email->setMessage($html);
            $email->setMailType('html');

            return $email->send();
        } catch (\Throwable $e) {
            log_message('error', 'Error enviando correo de confirmación a cliente: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar correo al cliente cuando su pedido pasa al estado "Entregado".
     */
    public static function sendOrderDeliveredEmail(object $order): bool
    {
        try {
            $customerEmail = trim((string)($order->customer_email ?? ''));
            if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                return false;
            }

            $subject = '📦 Tu pedido #' . $order->order_number . ' ha sido entregado — Dulce Gracia';
            $trackingLink = base_url('pedido/confirmacion/' . $order->order_number);
            $totalFmt     = '$' . number_format($order->total, 0, ',', '.');
            $deliveredDate = date('d/m/Y');
            $deliveredTime = date('h:i A');

            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 550px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden;'>
                <div style='background-color: #198754; padding: 25px; text-align: center; color: white;'>
                    <h1 style='margin: 0; font-size: 24px;'>📦 ¡Pedido Entregado!</h1>
                    <p style='margin: 5px 0 0 0;'>Dulce Gracia - Repostería</p>
                </div>
                <div style='padding: 25px;'>
                    <p>Hola <strong>" . esc($order->customer_name) . "</strong>,</p>
                    <p>Te confirmamos que tu pedido ha sido entregado exitosamente. ¡Esperamos que disfrutes cada bocado!</p>

                    <div style='background-color: #f8f9fa; padding: 18px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #198754;'>
                        <p style='margin: 4px 0;'><strong>📦 Pedido:</strong> {$order->order_number}</p>
                        <p style='margin: 4px 0;'><strong>📅 Fecha de Entrega:</strong> {$deliveredDate}</p>
                        <p style='margin: 4px 0;'><strong>🕐 Hora:</strong> {$deliveredTime}</p>
                        <p style='margin: 4px 0;'><strong>💰 Total:</strong> <strong style='color:#198754;'>{$totalFmt}</strong></p>
                    </div>

                    <p>Gracias por comprar en Dulce Gracia. Si deseas consultar los detalles de tu compra o dejar tu valoración, haz clic a continuación:</p>

                    <div style='text-align: center; margin: 25px 0;'>
                        <a href='{$trackingLink}' style='background-color: #d4a373; color: white; text-decoration: none; padding: 12px 25px; border-radius: 25px; font-weight: bold; font-size: 15px; display: inline-block;'>
                            Ver mi Pedido / Calificar
                        </a>
                    </div>
                </div>
                <div style='background-color: #f8f9fa; text-align: center; padding: 12px; font-size: 12px; color: #888;'>
                    Dulce Gracia © " . date('Y') . " - Gracias por preferirnos
                </div>
            </div>";

            $email = self::getEmailClient();
            $email->setTo($customerEmail);
            $fromEmail = setting('smtp_from_email', setting('contact_email', 'notificaciones@dulcegracia.com'));
            $fromName  = setting('smtp_from_name', setting('site_name', 'Dulce Gracia'));
            $email->setFrom($fromEmail, $fromName);
            $email->setSubject($subject);
            $email->setMessage($html);
            $email->setMailType('html');

            return $email->send();
        } catch (\Throwable $e) {
            log_message('error', 'Error enviando correo de pedido entregado: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar correo con enlace para restablecer la contraseña.
     */
    public static function sendPasswordResetEmail(string $recipientEmail, string $resetToken, bool $isAdmin = false): bool
    {
        try {
            $resetPath = $isAdmin ? 'admin/restablecer-contrasena/' : 'restablecer-contrasena/';
            $resetLink = base_url($resetPath . $resetToken);
            $subject   = $isAdmin
                ? '🔑 Restablecer contraseña de Administrador - Dulce Gracia'
                : '🔑 Restablecer tu contraseña - Dulce Gracia';

            $headerText  = $isAdmin ? 'Panel de Administración' : 'Dulce Gracia';
            $bodyContext = $isAdmin ? 'tu cuenta de administrador' : 'tu cuenta';

            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 550px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden;'>
                <div style='background-color: #d4a373; padding: 20px; text-align: center; color: white;'>
                    <h2 style='margin: 0;'>Dulce Gracia</h2>
                    <p style='margin: 5px 0 0 0;'>{$headerText} - Restablecimiento de Contraseña</p>
                </div>
                <div style='padding: 25px;'>
                    <p>Hola,</p>
                    <p>Recibimos una solicitud para restablecer la contraseña de {$bodyContext} en Dulce Gracia.</p>
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
