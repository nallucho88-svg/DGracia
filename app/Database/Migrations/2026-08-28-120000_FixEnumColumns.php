<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixEnumColumns extends Migration
{
    public function up()
    {
        // Corregir delivery_type en orders: 'pickup'/'delivery' → 'recoger'/'domicilio'
        $this->db->query("ALTER TABLE orders MODIFY COLUMN delivery_type ENUM('recoger','domicilio') NOT NULL DEFAULT 'domicilio'");

        // Corregir status en orders: English → Spanish
        $this->db->query("ALTER TABLE orders MODIFY COLUMN status ENUM('pendiente','en preparacion','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente'");

        // Corregir type en promotions: 'floating' → 'flotante'
        $this->db->query("ALTER TABLE promotions MODIFY COLUMN type ENUM('modal','flotante') NOT NULL DEFAULT 'modal'");
    }

    public function down()
    {
        // Revertir a los valores originales
        $this->db->query("ALTER TABLE orders MODIFY COLUMN delivery_type ENUM('pickup','delivery') NOT NULL DEFAULT 'delivery'");
        $this->db->query("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','preparing','on_way','delivered','cancelled') NOT NULL DEFAULT 'pending'");
        $this->db->query("ALTER TABLE promotions MODIFY COLUMN type ENUM('modal','floating') NOT NULL DEFAULT 'modal'");
    }
}
