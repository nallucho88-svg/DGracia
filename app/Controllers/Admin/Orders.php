<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\DeliveryZoneModel;

class Orders extends BaseController
{
    public function index()
    {
        $orderModel = new OrderModel();

        $startDate   = trim((string)$this->request->getGet('start_date'));
        $endDate     = trim((string)$this->request->getGet('end_date'));
        $orderNumber = trim((string)$this->request->getGet('order_number'));
        $customer    = trim((string)$this->request->getGet('customer'));

        if (!empty($startDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $orderModel->where('created_at >=', $startDate . ' 00:00:00');
        }
        if (!empty($endDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $orderModel->where('created_at <=', $endDate . ' 23:59:59');
        }
        if (!empty($orderNumber)) {
            $cleanNum = ltrim($orderNumber, '#');
            $orderModel->like('order_number', $cleanNum);
        }
        if (!empty($customer)) {
            $orderModel->groupStart()
                ->like('customer_name', $customer)
                ->orLike('customer_email', $customer)
                ->orLike('phone', $customer)
                ->groupEnd();
        }

        $orders = $orderModel->orderBy('created_at', 'DESC')->paginate(10);
        $pager  = $orderModel->pager;

        $totalFiltered = $pager->getTotal();
        $currentPage   = $pager->getCurrentPage();
        $perPage       = $pager->getPerPage();

        $fromItem = $totalFiltered > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
        $toItem   = min($currentPage * $perPage, $totalFiltered);

        $isFiltered = !empty($startDate) || !empty($endDate) || !empty($orderNumber) || !empty($customer);

        $data = [
            'title'         => 'Gestión de Pedidos | Admin',
            'orders'        => $orders,
            'pager'         => $pager,
            'startDate'     => $startDate,
            'endDate'       => $endDate,
            'orderNumber'   => $orderNumber,
            'customer'      => $customer,
            'isFiltered'    => $isFiltered,
            'totalFiltered' => $totalFiltered,
            'fromItem'      => $fromItem,
            'toItem'        => $toItem,
        ];

        return view('admin/orders/index', $data);
    }

    public function view($id)
    {
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $zoneModel = new DeliveryZoneModel();

        $order = $orderModel->find($id);

        if (!$order) {
            return redirect()->to(base_url('admin/pedidos'))->with('error', 'Pedido no encontrado.');
        }

        $items = $orderItemModel->where('order_id', $order->id)->findAll();
        
        $zone = null;
        if ($order->delivery_zone_id) {
            $zone = $zoneModel->find($order->delivery_zone_id);
        }

        $data = [
            'title' => 'Detalle del Pedido ' . $order->order_number . ' | Admin',
            'order' => $order,
            'items' => $items,
            'zone' => $zone
        ];

        return view('admin/orders/view', $data);
    }

    public function changeStatus()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('order_id');
            $status = $this->request->getPost('status');
            
            $validStatuses = ['pendiente', 'en preparacion', 'enviado', 'entregado', 'cancelado'];

            if (!in_array($status, $validStatuses)) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Estado inválido.']);
            }

            $orderModel = new OrderModel();
            
            if ($orderModel->update($id, ['status' => $status])) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Estado actualizado correctamente.']);
            }
            
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al actualizar.']);
        }

        return $this->response->setStatusCode(400);
    }

    /**
     * Retorna el detalle completo del pedido en JSON para modales y WhatsApp
     */
    public function detailJson($id)
    {
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();

        $order = $orderModel->find($id);

        if (!$order) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Pedido no encontrado']);
        }

        $items = $orderItemModel->where('order_id', $order->id)->findAll();

        // Formatear teléfono para Colombia (+57)
        $cleanPhone = preg_replace('/[^0-9]/', '', $order->phone ?? '');
        if (strlen($cleanPhone) === 10 && strpos($cleanPhone, '57') !== 0) {
            $cleanPhone = '57' . $cleanPhone;
        } elseif (strlen($cleanPhone) < 10) {
            $cleanPhone = '57' . $cleanPhone;
        }

        $originalSubtotal = 0;
        $productDiscountTotal = (float)($order->discount_amount ?? 0);
        
        $itemsList = "";
        foreach ($items as $item) {
            $origUnit  = (float)($item->original_price ?? $item->price);
            $finalUnit = (float)$item->price;
            $qty       = (int)$item->quantity;
            $originalSubtotal += ($origUnit * $qty);

            $origFmt  = '$' . number_format($origUnit, 0, ',', '.');
            $finalFmt = '$' . number_format($finalUnit, 0, ',', '.');

            $itemsList .= "{$item->product_name}\n";
            $itemsList .= "Cantidad: {$qty}\n";
            $itemsList .= "Precio original: {$origFmt}\n";
            $itemsList .= "Precio con descuento: {$finalFmt}\n\n";
        }

        $totalDiscount = $productDiscountTotal + (float)($order->coupon_discount ?? 0);
        $deliveryCost  = (float)($order->delivery_cost ?? 0);
        $finalTotal    = (float)$order->total;
        $orderDate     = !empty($order->created_at) ? date('d/m/Y h:i a', strtotime($order->created_at)) : date('d/m/Y');
        $supportPhone  = setting('contact_phone', '3108690810');
        $realValue     = ($originalSubtotal > 0) ? $originalSubtotal : ($order->subtotal + $totalDiscount);

        $waMessage = "Hola {$order->customer_name} 👋\n\n"
            . "Gracias por tu compra en Dulce Gracia.\n\n"
            . "🛍️ DETALLE DEL PEDIDO\n\n"
            . "Pedido: #{$order->order_number}\n"
            . "Fecha: {$orderDate}\n\n"
            . $itemsList
            . "💰 RESUMEN\n\n"
            . "Valor real: $" . number_format($realValue, 0, ',', '.') . "\n"
            . "Descuento: -$" . number_format($totalDiscount, 0, ',', '.') . "\n"
            . "Envío: $" . number_format($deliveryCost, 0, ',', '.') . "\n"
            . "TOTAL: $" . number_format($finalTotal, 0, ',', '.') . "\n\n"
            . "💳 MEDIOS DE PAGO\n\n"
            . "📲 Nequi / Daviplata: " . $supportPhone . "\n"
            . "🏦 Bancolombia Ahorros: 123-456789-01\n\n"
            . "📞 Soporte:\n"
            . $supportPhone;

        return $this->response->setJSON([
            'status'           => 'success',
            'order'            => $order,
            'items'            => $items,
            'cleanPhone'       => $cleanPhone,
            'originalSubtotal' => $realValue,
            'totalDiscount'    => $totalDiscount,
            'waMessage'        => $waMessage
        ]);
    }
}
