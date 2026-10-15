<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class Settings extends BaseController
{
    public function index()
    {
        $settingModel = new SettingModel();

        if ($this->request->getMethod() === 'POST') {
            $settingsData = $this->request->getPost('settings') ?? [];
            
            // 1. Favicon upload
            $favicon = $this->request->getFile('favicon');
            if ($favicon && $favicon->isValid() && !$favicon->hasMoved()) {
                $ext = strtolower($favicon->getClientExtension());
                if (in_array($ext, ['ico', 'png', 'gif', 'jpg', 'jpeg', 'svg', 'webp'])) {
                    $newName = 'favicon_' . time() . '.' . $ext;
                    $favicon->move(FCPATH . 'uploads/settings', $newName);
                    $settingsData['site_favicon'] = base_url('uploads/settings/' . $newName);
                    $settingsData['favicon_path'] = 'uploads/settings/' . $newName;
                }
            }

            // 2. Logo upload
            $logo = $this->request->getFile('logo');
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $newName = 'logo_' . time() . '.' . $logo->getClientExtension();
                $logo->move(FCPATH . 'uploads/settings', $newName);
                $settingsData['site_logo'] = base_url('uploads/settings/' . $newName);
            }

            // 3. Hero BG image upload
            $heroBg = $this->request->getFile('hero_bg_image');
            if ($heroBg && $heroBg->isValid() && !$heroBg->hasMoved()) {
                $newName = 'hero_' . time() . '.' . $heroBg->getClientExtension();
                $heroBg->move(FCPATH . 'uploads/settings', $newName);
                $settingsData['hero_bg_image'] = base_url('uploads/settings/' . $newName);
            }

            if (!empty($settingsData) && is_array($settingsData)) {
                foreach ($settingsData as $key => $value) {
                    $setting = $settingModel->where('key', $key)->first();
                    if ($setting) {
                        $settingModel->update($setting->id, ['value' => $value]);
                    } else {
                        $settingModel->insert([
                            'key'   => $key,
                            'value' => $value
                        ]);
                    }
                }
                return redirect()->to(base_url('admin/configuracion'))->with('success', 'Configuraciones actualizadas exitosamente.');
            }
        }

        $rawSettings = $settingModel->findAll();
        $settings = [];
        foreach ($rawSettings as $rs) {
            $settings[$rs->key] = $rs->value;
        }

        $defaultKeys = [
            'site_name', 'site_description', 'site_favicon', 'site_logo',
            'header_text', 'hero_title', 'hero_subtitle', 'hero_bg_image',
            'contact_address', 'contact_phone', 'contact_whatsapp', 'contact_email',
            'admin_notification_email',
            'business_hours', 'social_instagram', 'social_facebook', 'discount_stackable_mode',
            'alert_sound_enabled',
            'points_system_enabled', 'point_purchase_amount', 'point_reward_qty',
            'point_min_redeem', 'point_monetary_value', 'points_expiration_days',
            'points_calc_before_discount', 'points_allow_with_coupon',
            'smtp_host', 'smtp_user', 'smtp_pass', 'smtp_port', 'smtp_crypto', 'smtp_from_email', 'smtp_from_name'
        ];

        foreach ($defaultKeys as $key) {
            if (!isset($settings[$key])) {
                $settings[$key] = '';
            }
        }

        $data = [
            'title'    => 'Configuración General | Admin',
            'settings' => $settings
        ];

        return view('admin/settings/index', $data);
    }

    /**
     * Crear y Descargar Copia de Seguridad .SQL
     */
    public function createBackup()
    {
        try {
            $db = \Config\Database::connect();
            $tables = $db->listTables();

            $output = "-- Backup Base de Datos Dulce Gracia\n";
            $output .= "-- Generado el: " . date('Y-m-d H:i:s') . " (America/Bogota)\n\n";
            $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $createTableQuery = $db->query("SHOW CREATE TABLE `" . $table . "`")->getRowArray();
                $createSql = $createTableQuery['Create Table'] ?? '';
                $output .= "DROP TABLE IF EXISTS `" . $table . "`;\n";
                $output .= $createSql . ";\n\n";

                $rows = $db->query("SELECT * FROM `" . $table . "`")->getResultArray();
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $values = array_map(function ($val) use ($db) {
                            return ($val === null) ? 'NULL' : $db->escape($val);
                        }, array_values($row));

                        $cols = array_map(function ($col) {
                            return "`" . $col . "`";
                        }, array_keys($row));

                        $output .= "INSERT INTO `" . $table . "` (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $output .= "\n";
                }
            }

            $output .= "SET FOREIGN_KEY_CHECKS=1;\n";

            $backupFolder = WRITEPATH . 'backups/';
            if (!is_dir($backupFolder)) {
                mkdir($backupFolder, 0755, true);
            }

            $filename = 'dulce_gracia_backup_' . date('Y-m-d_His') . '.sql';
            $filepath = $backupFolder . $filename;
            file_put_contents($filepath, $output);

            return $this->response->download($filepath, null)->setFileName($filename);
        } catch (\Throwable $e) {
            log_message('error', 'Error creando backup: ' . $e->getMessage());
            return redirect()->to(base_url('admin/configuracion'))->with('error', 'Ocurrió un error al crear la copia de seguridad: ' . $e->getMessage());
        }
    }

    /**
     * Ejecutar y Verificar Migraciones Pendientes
     */
    public function runMigrations()
    {
        try {
            $migrations = \Config\Services::migrations();
            $migrated = $migrations->latest();

            if ($migrated) {
                return redirect()->to(base_url('admin/configuracion'))->with('success', '✓ Estructura de base de datos actualizada y verificada correctamente.');
            }

            return redirect()->to(base_url('admin/configuracion'))->with('success', '✓ La estructura de la base de datos ya está en la versión más reciente.');
        } catch (\Throwable $e) {
            log_message('error', 'Error en migración automática: ' . $e->getMessage());
            return redirect()->to(base_url('admin/configuracion'))->with('error', 'Error ejecutando la migración: ' . $e->getMessage());
        }
    }

    /**
     * Probar Conexión y Envío de Correo SMTP
     */
    public function testSmtp()
    {
        $testEmail = trim((string)$this->request->getPost('test_email'));
        if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $testEmail = setting('contact_email', 'admin@dulcegracia.com');
        }

        try {
            $emailClient = \App\Libraries\EmailService::getEmailClient();
            $emailClient->setTo($testEmail);
            $fromEmail = setting('smtp_from_email', setting('contact_email', 'notificaciones@dulcegracia.com'));
            $fromName  = setting('smtp_from_name', setting('site_name', 'Dulce Gracia'));
            $emailClient->setFrom($fromEmail, $fromName);
            $emailClient->setSubject('🧪 Correo de Prueba SMTP - Dulce Gracia');
            $emailClient->setMessage('<h3>🎉 ¡Configuración SMTP Funcional!</h3><p>Este es un correo de prueba enviado desde la configuración centralizada de la repostería Dulce Gracia.</p><small>Fecha: ' . date('d/m/Y h:i A') . '</small>');

            if ($emailClient->send()) {
                return redirect()->to(base_url('admin/configuracion'))->with('success', '✓ Correo de prueba enviado exitosamente a: ' . $testEmail);
            } else {
                $errorLog = $emailClient->printDebugger(['headers']);
                return redirect()->to(base_url('admin/configuracion'))->with('error', 'Error al enviar correo de prueba. Verifica la configuración SMTP. Detalle: ' . strip_tags($errorLog));
            }
        } catch (\Throwable $e) {
            return redirect()->to(base_url('admin/configuracion'))->with('error', 'Excepción enviando correo: ' . $e->getMessage());
        }
    }
}
