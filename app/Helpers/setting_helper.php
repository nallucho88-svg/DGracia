<?php

if (!function_exists('setting')) {
    /**
     * Helper global para obtener configuraciones centralizadas de la tienda.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = '')
    {
        static $settings = null;

        if ($settings === null) {
            $settings = [];
            try {
                $settingModel = new \App\Models\SettingModel();
                $raw = $settingModel->findAll();
                foreach ($raw as $item) {
                    $settings[$item->key] = $item->value;
                }
            } catch (\Throwable $e) {
                // Fallback si la DB no está lista
            }
        }

        $val = $settings[$key] ?? null;
        if ($val !== null && trim((string)$val) !== '') {
            return $val;
        }

        // Centralized fallbacks for contact & location
        $defaultsMap = [
            'contact_phone'    => '3108690810',
            'contact_whatsapp' => '3108690810',
            'contact_address'  => 'Medellín, Colombia',
            'site_city'        => 'Medellín, Colombia',
        ];

        if (!empty($default)) {
            return $default;
        }

        return $defaultsMap[$key] ?? $default;
    }
}
