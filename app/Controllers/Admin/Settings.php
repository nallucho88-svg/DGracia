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
            'business_hours', 'social_instagram', 'social_facebook', 'discount_stackable_mode',
            'alert_sound_enabled',
            'points_system_enabled', 'point_purchase_amount', 'point_reward_qty',
            'point_min_redeem', 'point_monetary_value', 'points_expiration_days',
            'points_calc_before_discount', 'points_allow_with_coupon'
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
}
