<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DulceGraciaSeeder extends Seeder
{
    public function run()
    {
        // 1. Crear usuario administrador
        $this->db->table('users')->insert([
            'name'       => 'Administrador',
            'email'      => 'admin@dulcegracia.com',
            'password'   => password_hash('12345678', PASSWORD_BCRYPT),
            'role'       => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // 2. Crear algunas categorías iniciales
        $categories = [
            ['name' => 'Tortas', 'slug' => 'tortas', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Galletas', 'slug' => 'galletas', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Postres', 'slug' => 'postres', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('categories')->insertBatch($categories);

        // 3. Crear configuraciones básicas
        $settings = [
            ['key' => 'BUSINESS_NAME', 'value' => 'Dulce Gracia', 'description' => 'Nombre de la empresa'],
            ['key' => 'BUSINESS_ADDRESS', 'value' => 'Medellín, Colombia', 'description' => 'Dirección de la repostería'],
            ['key' => 'BUSINESS_PHONE', 'value' => '3108690810', 'description' => 'Teléfono principal'],
            ['key' => 'WHATSAPP_NUMBER', 'value' => '573108690810', 'description' => 'Número de WhatsApp (con código de país sin +)'],
            ['key' => 'contact_phone', 'value' => '3108690810', 'description' => 'Teléfono de contacto'],
            ['key' => 'contact_whatsapp', 'value' => '3108690810', 'description' => 'WhatsApp de contacto'],
            ['key' => 'contact_address', 'value' => 'Medellín, Colombia', 'description' => 'Dirección de contacto'],
            ['key' => 'ADVISOR_NAME', 'value' => 'María', 'description' => 'Nombre de la asesora'],
            ['key' => 'BUSINESS_HOURS', 'value' => 'Lunes a sábado: 8:00 AM - 7:00 PM | Domingos: 9:00 AM - 3:00 PM', 'description' => 'Horarios de atención'],
            ['key' => 'FACEBOOK_URL', 'value' => 'https://facebook.com/dulcegracia', 'description' => 'URL de Facebook'],
            ['key' => 'INSTAGRAM_URL', 'value' => 'https://instagram.com/dulcegracia', 'description' => 'URL de Instagram'],
            ['key' => 'ABOUT_US_TEXT', 'value' => 'En Dulce Gracia endulzamos tus momentos especiales con las mejores recetas artesanales.', 'description' => 'Texto sobre nosotros'],
            ['key' => 'WHATSAPP_MESSAGE', 'value' => 'Hola, soy cliente de Dulce Gracia. 😊\nSe me antojó algo y quisiera realizar un pedido.\nMi nombre es:\nMi pedido es:\n¿Me pueden ayudar?', 'description' => 'Mensaje predeterminado de WhatsApp'],
        ];
        $this->db->table('settings')->insertBatch($settings);

        // 4. Crear zonas de domicilio básicas
        $delivery_zones = [
            ['name' => 'Zona 1 (Cercana)', 'cost' => 5000.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Zona 2 (Intermedia)', 'cost' => 7000.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Zona 3 (Lejana)', 'cost' => 10000.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('delivery_zones')->insertBatch($delivery_zones);
    }
}
