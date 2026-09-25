<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use CodeIgniter\Database\BaseConnection;

class Dashboard extends BaseController
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $startDate = trim($this->request->getGet('start_date') ?? '');
        $endDate   = trim($this->request->getGet('end_date') ?? '');

        $startDateTime = null;
        $endDateTime   = null;

        if (!empty($startDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $startDateTime = $startDate . ' 00:00:00';
        }
        if (!empty($endDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $endDateTime = $endDate . ' 23:59:59';
        }

        $isFiltered = ($startDateTime !== null || $endDateTime !== null);

        // Cláusula WHERE base para la tabla orders (respetando zona horaria America/Bogota)
        $whereOrder  = " WHERE 1=1 ";
        $whereOrderO = "";
        if ($startDateTime) {
            $whereOrder  .= " AND created_at >= " . $this->db->escape($startDateTime);
            $whereOrderO .= " AND o.created_at >= " . $this->db->escape($startDateTime);
        }
        if ($endDateTime) {
            $whereOrder  .= " AND created_at <= " . $this->db->escape($endDateTime);
            $whereOrderO .= " AND o.created_at <= " . $this->db->escape($endDateTime);
        }

        // ── KPIs de pedidos y catálogo ──────────────────────────────────────────
        $totalOrders   = (int) ($this->db->query("SELECT COUNT(*) AS cnt FROM orders {$whereOrder}")->getRow()->cnt ?? 0);
        $pendingOrders = (int) ($this->db->query("SELECT COUNT(*) AS cnt FROM orders {$whereOrder} AND status = 'pendiente'")->getRow()->cnt ?? 0);
        
        $productModel  = new ProductModel();
        $totalProducts = $productModel->countAllResults();

        $customerWhere = " WHERE role = 'customer' ";
        if ($startDateTime) {
            $customerWhere .= " AND created_at >= " . $this->db->escape($startDateTime);
        }
        if ($endDateTime) {
            $customerWhere .= " AND created_at <= " . $this->db->escape($endDateTime);
        }
        $totalCustomers = (int) ($this->db->query("SELECT COUNT(*) AS cnt FROM users {$customerWhere}")->getRow()->cnt ?? 0);

        // ── Ventas Totales en el Rango Seleccionado ────────────────────────────
        $salesTotal = (float) ($this->db->query(
            "SELECT COALESCE(SUM(total),0) AS val FROM orders {$whereOrder} AND status != 'cancelado'"
        )->getRow()->val ?? 0);

        // Ventas por periodos prefijados (referenciales)
        $salesToday = (float) ($this->db->query(
            "SELECT COALESCE(SUM(total),0) AS val FROM orders WHERE status != 'cancelado' AND DATE(created_at) = CURDATE()"
        )->getRow()->val ?? 0);

        $salesWeek = (float) ($this->db->query(
            "SELECT COALESCE(SUM(total),0) AS val FROM orders WHERE status != 'cancelado' AND YEARWEEK(created_at,1) = YEARWEEK(CURDATE(),1)"
        )->getRow()->val ?? 0);

        $salesMonth = (float) ($this->db->query(
            "SELECT COALESCE(SUM(total),0) AS val FROM orders WHERE status != 'cancelado' AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())"
        )->getRow()->val ?? 0);

        $salesYear = (float) ($this->db->query(
            "SELECT COALESCE(SUM(total),0) AS val FROM orders WHERE status != 'cancelado' AND YEAR(created_at) = YEAR(CURDATE())"
        )->getRow()->val ?? 0);

        // ── Pedidos por estado ───────────────────────────────────────────────
        $ordersByStatus = $this->db->query(
            "SELECT status, COUNT(*) AS cnt FROM orders {$whereOrder} GROUP BY status ORDER BY cnt DESC"
        )->getResultArray();

        // ── Ventas diarias por canal (Web vs POS) ─────────────────────────────
        if ($startDateTime && $endDateTime) {
            $periodStart = new \DateTime($startDateTime);
            $periodEnd   = new \DateTime($endDateTime);
        } else if ($startDateTime) {
            $periodStart = new \DateTime($startDateTime);
            $periodEnd   = new \DateTime();
        } else if ($endDateTime) {
            $periodStart = new \DateTime(date('Y-m-01 00:00:00', strtotime($endDate)));
            $periodEnd   = new \DateTime($endDateTime);
        } else {
            $periodStart = new \DateTime(date('Y-m-01 00:00:00'));
            $periodEnd   = new \DateTime(date('Y-m-t 23:59:59'));
        }

        $dailyWebRaw = $this->db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m-%d') AS day_date,
                    COALESCE(SUM(total),0) AS total_sales
             FROM orders {$whereOrder}
               AND status != 'cancelado'
               AND source = 'web'
             GROUP BY day_date ORDER BY day_date ASC"
        )->getResultArray();

        $dailyPosRaw = $this->db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m-%d') AS day_date,
                    COALESCE(SUM(total),0) AS total_sales
             FROM orders {$whereOrder}
               AND status != 'cancelado'
               AND source = 'pos'
             GROUP BY day_date ORDER BY day_date ASC"
        )->getResultArray();

        $dailyLabels = [];
        $dailyWebSales = [];
        $dailyPosSales = [];

        $interval = new \DateInterval('P1D');
        $periodEndInclusive = (clone $periodEnd)->modify('+1 day');
        $daterange = new \DatePeriod($periodStart, $interval, $periodEndInclusive);

        foreach ($daterange as $date) {
            $dateStr = $date->format('Y-m-d');
            $dailyLabels[] = $date->format('d/m');

            $webVal = 0;
            foreach ($dailyWebRaw as $r) {
                if ($r['day_date'] === $dateStr) {
                    $webVal = (float)$r['total_sales'];
                    break;
                }
            }
            $dailyWebSales[] = $webVal;

            $posVal = 0;
            foreach ($dailyPosRaw as $r) {
                if ($r['day_date'] === $dateStr) {
                    $posVal = (float)$r['total_sales'];
                    break;
                }
            }
            $dailyPosSales[] = $posVal;
        }

        // ── Ventas y ganancias mensuales ──────────────────────────────────────

        $monthlySalesRaw = $this->db->query(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') AS month,
                    COALESCE(SUM(total),0) AS total_sales
             FROM orders {$whereOrder}
               AND status != 'cancelado'
             GROUP BY month ORDER BY month ASC"
        )->getResultArray();

        $monthlyProfitsRaw = $this->db->query(
            "SELECT DATE_FORMAT(o.created_at,'%Y-%m') AS month,
                    COALESCE(SUM(oi.quantity * (oi.price - COALESCE(p.cost,0))),0) AS profit
             FROM orders o
             JOIN order_items oi ON oi.order_id = o.id
             JOIN products p ON p.id = oi.product_id
             WHERE o.status != 'cancelado' {$whereOrderO}
             GROUP BY month ORDER BY month ASC"
        )->getResultArray();

        $months = [];
        if ($isFiltered) {
            foreach ($monthlySalesRaw as $r) {
                if (!in_array($r['month'], $months)) {
                    $months[] = $r['month'];
                }
            }
            if (empty($months)) {
                $months[] = date('Y-m');
            }
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $months[] = date('Y-m', strtotime("-{$i} months"));
            }
        }

        $salesByMonth  = array_fill_keys($months, 0);
        $profitByMonth = array_fill_keys($months, 0);

        foreach ($monthlySalesRaw as $row) {
            if (isset($salesByMonth[$row['month']])) {
                $salesByMonth[$row['month']] = (float) $row['total_sales'];
            }
        }
        foreach ($monthlyProfitsRaw as $row) {
            if (isset($profitByMonth[$row['month']])) {
                $profitByMonth[$row['month']] = (float) $row['profit'];
            }
        }

        $bestMonth  = !empty($salesByMonth) && max($salesByMonth) > 0 ? array_search(max($salesByMonth), $salesByMonth) : null;
        $worstMonth = !empty($salesByMonth) && min($salesByMonth) > 0 ? array_search(min($salesByMonth), $salesByMonth) : null;

        $monthsEs = [
            '01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr',
            '05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Ago',
            '09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic'
        ];
        $monthLabels = array_map(function ($m) use ($monthsEs) {
            [$y, $mon] = explode('-', $m);
            return ($monthsEs[$mon] ?? $mon) . ' ' . $y;
        }, $months);

        // ── Top Productos del Rango ───────────────────────────────────────────
        $bestSellingProduct = $this->db->query(
            "SELECT p.id, p.name, p.price, p.cost,
                    (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_main DESC, pi.id ASC LIMIT 1) AS image_path,
                    SUM(oi.quantity) AS total_qty,
                    SUM(oi.quantity * oi.price) AS total_revenue
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             JOIN orders o ON o.id = oi.order_id
             WHERE o.status != 'cancelado' {$whereOrderO}
             GROUP BY p.id, p.name, p.price, p.cost
             ORDER BY total_qty DESC LIMIT 1"
        )->getRow();

        $leastSellingProduct = $this->db->query(
            "SELECT p.id, p.name, p.price, p.cost,
                    (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_main DESC, pi.id ASC LIMIT 1) AS image_path,
                    SUM(oi.quantity) AS total_qty,
                    SUM(oi.quantity * oi.price) AS total_revenue
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             JOIN orders o ON o.id = oi.order_id
             WHERE o.status != 'cancelado' {$whereOrderO}
             GROUP BY p.id, p.name, p.price, p.cost
             ORDER BY total_qty ASC LIMIT 1"
        )->getRow();

        $mostProfitableProduct = $this->db->query(
            "SELECT p.id, p.name, p.price, p.cost,
                    (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_main DESC, pi.id ASC LIMIT 1) AS image_path,
                    SUM(oi.quantity * (oi.price - COALESCE(p.cost,0))) AS total_profit,
                    SUM(oi.quantity) AS total_qty
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             JOIN orders o ON o.id = oi.order_id
             WHERE o.status != 'cancelado' {$whereOrderO}
             GROUP BY p.id, p.name, p.price, p.cost
             ORDER BY total_profit DESC LIMIT 1"
        )->getRow();

        // ── Últimos pedidos del rango o globales ──────────────────────────────
        $orderModel = new OrderModel();
        if ($isFiltered) {
            $builder = $orderModel;
            if ($startDateTime) $builder->where('created_at >=', $startDateTime);
            if ($endDateTime)   $builder->where('created_at <=', $endDateTime);
            $recentOrders = $builder->orderBy('created_at', 'DESC')->limit(10)->findAll();
        } else {
            $recentOrders = $orderModel->orderBy('created_at', 'DESC')->limit(8)->findAll();
        }

        $data = [
            'title'                 => 'Dashboard | Admin',
            'startDate'             => $startDate,
            'endDate'               => $endDate,
            'isFiltered'            => $isFiltered,
            // KPIs
            'totalOrders'           => $totalOrders,
            'pendingOrders'         => $pendingOrders,
            'totalProducts'         => $totalProducts,
            'totalCustomers'        => $totalCustomers,
            'salesTotal'            => $salesTotal,
            'salesToday'            => $salesToday,
            'salesWeek'             => $salesWeek,
            'salesMonth'            => $salesMonth,
            'salesYear'             => $salesYear,
            // Chart data Web vs POS
            'dailyLabels'           => $dailyLabels,
            'dailyWebSales'         => $dailyWebSales,
            'dailyPosSales'         => $dailyPosSales,
            'monthLabels'           => $monthLabels,
            'salesByMonth'          => array_values($salesByMonth),
            'profitByMonth'         => array_values($profitByMonth),
            'bestMonth'             => $bestMonth,
            'worstMonth'            => $worstMonth,
            // Orders by status
            'ordersByStatus'        => $ordersByStatus,
            // Top products
            'bestSellingProduct'    => $bestSellingProduct,
            'leastSellingProduct'   => $leastSellingProduct,
            'mostProfitableProduct' => $mostProfitableProduct,
            // Recent orders
            'recentOrders'          => $recentOrders,
        ];

        return view('admin/dashboard/index', $data);
    }
}
