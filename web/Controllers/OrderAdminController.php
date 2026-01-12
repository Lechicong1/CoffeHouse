<?php
/**
 * OrderAdminController - Quản lý đơn hàng cho Admin
 * Hiển thị danh sách đơn hàng và xuất Excel
 */
require_once __DIR__ . '/../../Config/ExcelHelper.php';
require_once __DIR__ . '/../Repositories/OrderRepository.php';

class OrderAdminController extends Controller {
    private $orderRepository;

    function __construct() {
        $this->orderRepository = new OrderRepository();
    }

    /**
     * Lấy danh sách tất cả đơn hàng (Method mặc định)
     */
    function GetData() {
        $orders = $this->getAllOrders();

        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Order_v',
            'section' => 'orders',
            'orders' => $orders,
            'keyword' => '',
            'totalOrders' => count($orders)
        ]);
    }

    /**
     * Tìm kiếm đơn hàng theo mã hoặc tên/sđt người nhận
     */
    function timkiem() {
        if (isset($_POST['btnTimkiem'])) {
            $keyword = $_POST['txtSearch'] ?? '';
            $orders = $this->searchOrders($keyword);

            $this->view('AdminDashBoard/MasterLayout', [
                'page' => 'Order_v',
                'section' => 'orders',
                'orders' => $orders,
                'keyword' => $keyword,
                'totalOrders' => count($orders)
            ]);
        }
    }

    /**
     * Xuất Excel danh sách đơn hàng
     */
    function xuatexcel() {
        if (isset($_POST['btnXuatexcel'])) {
            $keyword = isset($_POST['txtSearch']) ? $_POST['txtSearch'] : '';

            if (!empty($keyword)) {
                $orders = $this->searchOrders($keyword);
            } else {
                $orders = $this->getAllOrders();
            }

            // Chuyển đổi dữ liệu sang array cho Excel
            $data = array_map(function($order) {
                return [
                    'order_code' => $order['order_code'],
                    'status' => $this->getStatusLabel($order['status']),
                    'payment_status' => $this->getPaymentStatusLabel($order['payment_status']),
                    'total_amount' => number_format($order['total_amount'], 0, ',', '.') . 'đ',
                    'receiver_name' => $order['receiver_name'] ?? '-',
                    'receiver_phone' => $order['receiver_phone'] ?? '-'
                ];
            }, $orders);

            // Định nghĩa tiêu đề cột
            $headers = [
                'order_code' => 'Mã Đơn Hàng',
                'status' => 'Trạng Thái',
                'payment_status' => 'Thanh Toán',
                'total_amount' => 'Tổng Tiền',
                'receiver_name' => 'Tên Người Nhận',
                'receiver_phone' => 'SĐT Người Nhận'
            ];

            ExcelHelper::exportToExcel($data, $headers, 'DanhSachDonHang');
        }
    }

    /**
     * Lấy tất cả đơn hàng từ database
     */
    private function getAllOrders() {
        $sql = "SELECT order_code, status, payment_status, total_amount, receiver_name, receiver_phone FROM orders ORDER BY id DESC";
        $result = mysqli_query($this->orderRepository->con, $sql);

        $orders = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $orders[] = $row;
            }
        }
        return $orders;
    }

    /**
     * Tìm kiếm đơn hàng theo keyword
     */
    private function searchOrders($keyword) {
        $keyword = '%' . mysqli_real_escape_string($this->orderRepository->con, $keyword) . '%';

        $sql = "SELECT order_code, status, payment_status, total_amount, receiver_name, receiver_phone 
                FROM orders 
                WHERE order_code LIKE ? OR receiver_name LIKE ? OR receiver_phone LIKE ?
                ORDER BY id DESC";

        $stmt = mysqli_prepare($this->orderRepository->con, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $keyword, $keyword, $keyword);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        return $orders;
    }

    /**
     * Chuyển đổi status sang tiếng Việt
     */
    private function getStatusLabel($status) {
        $labels = [
            'PENDING' => 'Chờ xử lý',
            'AWAITING_PAYMENT' => 'Chờ thanh toán',
            'PREPARING' => 'Đang pha chế',
            'READY' => 'Sẵn sàng',
            'SHIPPING' => 'Đang giao',
            'COMPLETED' => 'Hoàn thành',
            'CANCELLED' => 'Đã hủy'
        ];
        return $labels[$status] ?? $status;
    }

    /**
     * Chuyển đổi payment_status sang tiếng Việt
     */
    private function getPaymentStatusLabel($paymentStatus) {
        $labels = [
            'PENDING' => 'Chưa thanh toán',
            'AWAITING_PAYMENT' => 'Chờ thanh toán',
            'PAID' => 'Đã thanh toán',
            'REFUNDED' => 'Đã hoàn tiền',
            'UNPAID' => 'Chưa thanh toán'
        ];
        return $labels[$paymentStatus] ?? $paymentStatus;
    }
}

