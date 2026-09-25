<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDiscountsAndEnhancements extends Migration
{
    public function up()
    {
        // 1. Agregar is_loyal en users
        if (!$this->db->fieldExists('is_loyal', 'users')) {
            $this->forge->addColumn('users', [
                'is_loyal' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'role']
            ]);
        }

        // 2. Agregar cost en products
        if (!$this->db->fieldExists('cost', 'products')) {
            $this->forge->addColumn('products', [
                'cost' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00', 'after' => 'price']
            ]);
        }

        // 3. Crear tabla discounts
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'          => ['type' => 'VARCHAR', 'constraint' => '150'],
            'type'           => ['type' => 'ENUM', 'constraint' => ['product', 'category', 'loyalty', 'general'], 'default' => 'general'],
            'discount_type'  => ['type' => 'ENUM', 'constraint' => ['percentage', 'fixed'], 'default' => 'percentage'],
            'value'          => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'product_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'category_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'start_date'     => ['type' => 'DATETIME', 'null' => true],
            'end_date'       => ['type' => 'DATETIME', 'null' => true],
            'status'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('discounts', true);

        // 4. Crear tabla coupons
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'                => ['type' => 'VARCHAR', 'constraint' => '150'],
            'description'         => ['type' => 'TEXT', 'null' => true],
            'code'                => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => true],
            'discount_type'       => ['type' => 'ENUM', 'constraint' => ['percentage', 'fixed'], 'default' => 'percentage'],
            'value'               => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'start_date'          => ['type' => 'DATETIME', 'null' => true],
            'end_date'            => ['type' => 'DATETIME', 'null' => true],
            'max_uses'            => ['type' => 'INT', 'constraint' => 11, 'default' => 0], // 0 = ilimitado
            'used_count'          => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'max_uses_per_user'   => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'category_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'product_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'loyal_only'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_stackable'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'status'              => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('coupons', true);

        // 5. Crear tabla coupon_logs
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'coupon_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'order_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('coupon_id', 'coupons', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('coupon_logs', true);

        // 6. Crear tabla password_resets
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email'       => ['type' => 'VARCHAR', 'constraint' => '150'],
            'token_hash'  => ['type' => 'VARCHAR', 'constraint' => '255'],
            'expires_at'  => ['type' => 'DATETIME'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('password_resets', true);
    }

    public function down()
    {
        $this->forge->dropTable('password_resets', true);
        $this->forge->dropTable('coupon_logs', true);
        $this->forge->dropTable('coupons', true);
        $this->forge->dropTable('discounts', true);

        if ($this->db->fieldExists('cost', 'products')) {
            $this->forge->dropColumn('products', 'cost');
        }

        if ($this->db->fieldExists('is_loyal', 'users')) {
            $this->forge->dropColumn('users', 'is_loyal');
        }
    }
}
