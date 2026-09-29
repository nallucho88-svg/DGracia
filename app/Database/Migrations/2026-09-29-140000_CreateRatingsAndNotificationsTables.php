<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRatingsAndNotificationsTables extends Migration
{
    public function up()
    {
        // 1. Crear tabla ratings
        if (!$this->db->tableExists('ratings')) {
            $this->forge->addField([
                'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'order_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'order_item_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'product_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'rating'        => ['type' => 'TINYINT', 'constraint' => 1],
                'comment'       => ['type' => 'TEXT', 'null' => true],
                'status'        => ['type' => 'ENUM', 'constraint' => ['visible', 'oculta', 'eliminada'], 'default' => 'visible'],
                'created_at'    => ['type' => 'DATETIME', 'null' => true],
                'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['product_id', 'status']);
            $this->forge->addUniqueKey('order_item_id');
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('order_item_id', 'order_items', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('ratings', true);
        }

        // 2. Crear tabla notifications
        if (!$this->db->tableExists('notifications')) {
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'type'       => ['type' => 'VARCHAR', 'constraint' => '50', 'default' => 'system'],
                'title'      => ['type' => 'VARCHAR', 'constraint' => '150'],
                'message'    => ['type' => 'TEXT'],
                'target_url' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
                'is_read'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('is_read');
            $this->forge->createTable('notifications', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('notifications', true);
        $this->forge->dropTable('ratings', true);
    }
}
