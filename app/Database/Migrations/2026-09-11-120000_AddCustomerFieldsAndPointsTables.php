<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerFieldsAndPointsTables extends Migration
{
    public function up()
    {
        // 1. Agregar campos faltantes en users
        $fieldsToAdd = [];
        if (!$this->db->fieldExists('lastname', 'users')) {
            $fieldsToAdd['lastname'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true, 'after' => 'name'];
        }
        if (!$this->db->fieldExists('phone', 'users')) {
            $fieldsToAdd['phone'] = ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true, 'after' => 'email'];
        }
        if (!$this->db->fieldExists('whatsapp', 'users')) {
            $fieldsToAdd['whatsapp'] = ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true, 'after' => 'phone'];
        }
        if (!$this->db->fieldExists('points', 'users')) {
            $fieldsToAdd['points'] = ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'is_loyal'];
        }
        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('users', $fieldsToAdd);
        }

        // 2. Agregar source en orders ('web', 'pos')
        if (!$this->db->fieldExists('source', 'orders')) {
            $this->forge->addColumn('orders', [
                'source' => ['type' => 'ENUM', 'constraint' => ['web', 'pos'], 'default' => 'web', 'after' => 'notes']
            ]);
        }

        // 3. Crear tabla favorites
        if (!$this->db->tableExists('favorites')) {
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('favorites', true);
        }

        // 4. Crear tabla point_transactions
        if (!$this->db->tableExists('point_transactions')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'order_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'points'      => ['type' => 'INT', 'constraint' => 11],
                'type'        => ['type' => 'ENUM', 'constraint' => ['earned', 'spent', 'expired', 'adjustment'], 'default' => 'earned'],
                'description' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('point_transactions', true);
        }

        // 5. Crear tabla point_goals
        if (!$this->db->tableExists('point_goals')) {
            $this->forge->addField([
                'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'title'           => ['type' => 'VARCHAR', 'constraint' => '150'],
                'points_required' => ['type' => 'INT', 'constraint' => 11],
                'reward_type'     => ['type' => 'ENUM', 'constraint' => ['coupon', 'discount', 'free_product'], 'default' => 'coupon'],
                'reward_value'    => ['type' => 'VARCHAR', 'constraint' => '255'],
                'status'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at'      => ['type' => 'DATETIME', 'null' => true],
                'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('point_goals', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('point_goals', true);
        $this->forge->dropTable('point_transactions', true);
        $this->forge->dropTable('favorites', true);

        if ($this->db->fieldExists('source', 'orders')) {
            $this->forge->dropColumn('orders', 'source');
        }

        $fieldsToDrop = [];
        if ($this->db->fieldExists('points', 'users')) $fieldsToDrop[] = 'points';
        if ($this->db->fieldExists('whatsapp', 'users')) $fieldsToDrop[] = 'whatsapp';
        if ($this->db->fieldExists('phone', 'users')) $fieldsToDrop[] = 'phone';
        if ($this->db->fieldExists('lastname', 'users')) $fieldsToDrop[] = 'lastname';

        if (!empty($fieldsToDrop)) {
            $this->forge->dropColumn('users', $fieldsToDrop);
        }
    }
}
