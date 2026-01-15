<?php
/**
 * OrderAdminController - Quản lý đơn hàng cho Admin
 * Hiển thị danh sách đơn hàng và xuất Excel
 */
require_once __DIR__ . '/../../Config/ExcelHelper.php';
require_once __DIR__ . '/../Services/OrderService.php';

class OrderAdminController extends Controller {
    private $orderService;

    function __construct() {
        $this->orderService = new OrderService();
    }

    /**
     * Lấy danh sách tất cả đơn hàng (Method mặc định)
     */
    function GetData() {
        $orders = $this->orderService->getAllOrdersForAdmin();

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
            $orders = $this->orderService->searchOrdersForAdmin($keyword);

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
                $orders = $this->orderService->searchOrdersForAdmin($keyword);
            } else {
                $orders = $this->orderService->getAllOrdersForAdmin();
            }

            // Chuyển đổi dữ liệu sang array cho Excel
            $data = array_map(function($order) {
                return [
                    'order_code' => $order['order_code'],
                    'order_type' => $order['order_type'] ?? '-',
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
                'order_type' => 'Loại Đơn Hàng',
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
     * Chuyển đổi status sang tiếng Việt
     */
    private function getStatusLabel($status) {
        $labels = [
            'PENDING' => 'Chờ xử lý',
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
            'PAID' => 'Đã thanh toán',
        ];
        return $labels[$paymentStatus] ?? $paymentStatus;
    }
}

