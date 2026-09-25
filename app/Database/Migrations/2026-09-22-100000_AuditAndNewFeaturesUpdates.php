<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AuditAndNewFeaturesUpdates extends Migration
{
    public function up()
    {
        // 1. Nuevos campos en orders
        $orderFields = [];
        if (!$this->db->fieldExists('customer_email', 'orders')) {
            $orderFields['customer_email'] = ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true, 'after' => 'phone'];
        }
        if (!$this->db->fieldExists('coupon_code', 'orders')) {
            $orderFields['coupon_code'] = ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true, 'after' => 'delivery_cost'];
        }
        if (!$this->db->fieldExists('coupon_discount', 'orders')) {
            $orderFields['coupon_discount'] = ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00', 'after' => 'coupon_code'];
        }
        if (!$this->db->fieldExists('discount_amount', 'orders')) {
            $orderFields['discount_amount'] = ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00', 'after' => 'coupon_discount'];
        }
        if (!$this->db->fieldExists('payment_status', 'orders')) {
            $orderFields['payment_status'] = ['type' => 'ENUM', 'constraint' => ['pendiente', 'pagado', 'rechazado'], 'default' => 'pendiente', 'after' => 'payment_method'];
        }
        if (!empty($orderFields)) {
            $this->forge->addColumn('orders', $orderFields);
        }

        // 2. Nuevos campos en order_items
        $itemFields = [];
        if (!$this->db->fieldExists('sku', 'order_items')) {
            $itemFields['sku'] = ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true, 'after' => 'product_name'];
        }
        if (!$this->db->fieldExists('original_price', 'order_items')) {
            $itemFields['original_price'] = ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'after' => 'price'];
        }
        if (!$this->db->fieldExists('offer_price', 'order_items')) {
            $itemFields['offer_price'] = ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'after' => 'original_price'];
        }
        if (!$this->db->fieldExists('discount_amount', 'order_items')) {
            $itemFields['discount_amount'] = ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00', 'after' => 'offer_price'];
        }
        if (!empty($itemFields)) {
            $this->forge->addColumn('order_items', $itemFields);
        }

        // 3. Crear tabla user_cart (Carrito persistente para usuarios registrados)
        if (!$this->db->tableExists('user_cart')) {
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'quantity'   => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('user_cart', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('user_cart', true);

        $orderFieldsToDrop = [];
        if ($this->db->fieldExists('payment_status', 'orders')) $orderFieldsToDrop[] = 'payment_status';
        if ($this->db->fieldExists('discount_amount', 'orders')) $orderFieldsToDrop[] = 'discount_amount';
        if ($this->db->fieldExists('coupon_discount', 'orders')) $orderFieldsToDrop[] = 'coupon_discount';
        if ($this->db->fieldExists('coupon_code', 'orders')) $orderFieldsToDrop[] = 'coupon_code';
        if ($this->db->fieldExists('customer_email', 'orders')) $orderFieldsToDrop[] = 'customer_email';

        if (!empty($orderFieldsToDrop)) {
            $this->forge->dropColumn('orders', $orderFieldsToDrop);
        }

        $itemFieldsToDrop = [];
        if ($this->db->fieldExists('discount_amount', 'order_items')) $itemFieldsToDrop[] = 'discount_amount';
        if ($this->db->fieldExists('offer_price', 'order_items')) $itemFieldsToDrop[] = 'offer_price';
        if ($this->db->fieldExists('original_price', 'order_items')) $itemFieldsToDrop[] = 'original_price';
        if ($this->db->fieldExists('sku', 'order_items')) $itemFieldsToDrop[] = 'sku';

        if (!empty($itemFieldsToDrop)) {
            $this->forge->dropColumn('order_items', $itemFieldsToDrop);
        }
    }
}
