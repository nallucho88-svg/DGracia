<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDulceGraciaTables extends Migration
{
    public function up()
    {
        // 1. users table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => '100'],
            'email'      => ['type' => 'VARCHAR', 'constraint' => '150', 'unique' => true],
            'password'   => ['type' => 'VARCHAR', 'constraint' => '255'],
            'role'       => ['type' => 'ENUM', 'constraint' => ['admin', 'customer'], 'default' => 'customer'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);

        // 2. categories table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => '100'],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => '100', 'unique' => true],
            'status'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('categories', true);

        // 3. products table
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'category_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name'              => ['type' => 'VARCHAR', 'constraint' => '150'],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => '150', 'unique' => true],
            'short_description' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'description'       => ['type' => 'TEXT', 'null' => true],
            'price'             => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'ingredients'       => ['type' => 'TEXT', 'null' => true],
            'portions'          => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'availability'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'status'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('products', true);

        // 4. product_images table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'image_path' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'is_main'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('product_images', true);

        // 5. delivery_zones table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => '100'],
            'cost'       => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'status'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('delivery_zones', true);

        // 6. orders table
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_number'          => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => true],
            'user_id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'customer_name'         => ['type' => 'VARCHAR', 'constraint' => '150'],
            'phone'                 => ['type' => 'VARCHAR', 'constraint' => '50'],
            'whatsapp'              => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'address'               => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'neighborhood'          => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'city'                  => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'delivery_instructions' => ['type' => 'TEXT', 'null' => true],
            'delivery_date'         => ['type' => 'DATE', 'null' => true],
            'delivery_time'         => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'delivery_type'         => ['type' => 'ENUM', 'constraint' => ['pickup', 'delivery'], 'default' => 'delivery'],
            'delivery_zone_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'delivery_cost'         => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'subtotal'              => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'total'                 => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'payment_method'        => ['type' => 'VARCHAR', 'constraint' => '50'],
            'status'                => ['type' => 'ENUM', 'constraint' => ['pending', 'confirmed', 'preparing', 'on_way', 'delivered', 'cancelled'], 'default' => 'pending'],
            'notes'                 => ['type' => 'TEXT', 'null' => true],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
            'updated_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('delivery_zone_id', 'delivery_zones', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('orders', true);

        // 7. order_items table
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'product_name' => ['type' => 'VARCHAR', 'constraint' => '150'],
            'price'        => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'quantity'     => ['type' => 'INT', 'constraint' => 11],
            'subtotal'     => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('order_items', true);

        // 8. banners table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true],
            'image_path' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'link'       => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'position'   => ['type' => 'VARCHAR', 'constraint' => '50', 'default' => 'home'],
            'priority'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'status'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('banners', true);

        // 9. promotions table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'       => ['type' => 'VARCHAR', 'constraint' => '150'],
            'description' => ['type' => 'TEXT', 'null' => true],
            'image_path'  => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'type'        => ['type' => 'ENUM', 'constraint' => ['modal', 'floating'], 'default' => 'modal'],
            'status'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('promotions', true);

        // 10. messages table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => '150'],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => '50'],
            'email'      => ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true],
            'type'       => ['type' => 'ENUM', 'constraint' => ['Sugerencia', 'Felicitación', 'Queja', 'Reclamo'], 'default' => 'Sugerencia'],
            'message'    => ['type' => 'TEXT'],
            'status'     => ['type' => 'ENUM', 'constraint' => ['unread', 'read'], 'default' => 'unread'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('messages', true);

        // 11. settings table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'key'         => ['type' => 'VARCHAR', 'constraint' => '100', 'unique' => true],
            'value'       => ['type' => 'TEXT', 'null' => true],
            'description' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('settings', true);
        $this->forge->dropTable('messages', true);
        $this->forge->dropTable('promotions', true);
        $this->forge->dropTable('banners', true);
        $this->forge->dropTable('order_items', true);
        $this->forge->dropTable('orders', true);
        $this->forge->dropTable('delivery_zones', true);
        $this->forge->dropTable('product_images', true);
        $this->forge->dropTable('products', true);
        $this->forge->dropTable('categories', true);
        $this->forge->dropTable('users', true);
    }
}
