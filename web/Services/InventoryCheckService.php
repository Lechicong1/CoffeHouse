<?php
/**
 * InventoryCheckService - Xử lý logic nghiệp vụ cho Kiểm kho
 */
class InventoryCheckService extends Service {

    private $inventoryCheckRepo;

    public function __construct() {
        // Khởi tạo Repository thông qua Service base
        $this->inventoryCheckRepo = $this->repository('InventoryCheckRepository');
    }

    /**
     * Lấy danh sách kiểm kho theo ngày
     * @param string|null $date Ngày kiểm tra (Mặc định: hôm nay)
     * @return array
     */
    public function getInventoryCheckByDate($date = null) {
        // Nếu không truyền ngày, lấy ngày hôm nay
        if ($date === null) {
            $date = date('Y-m-d');
        }

        return $this->inventoryCheckRepo->getInventoryCheckByDate($date);
    }

    /**
     * Lưu thông tin kiểm kho
     * @param array $data
     * @return bool
     */
    public function saveInventoryCheck($data) {
        // Validate dữ liệu
        $this->validateInventoryCheckData($data);

        // Tính toán chênh lệch
        $data['difference'] = $data['actualQuantity'] - $data['theoryQuantity'];

        return $this->inventoryCheckRepo->saveInventoryCheck($data);
    }

    /**
     * Tính toán thông tin kiểm kho
     * @param float $theoryQuantity Số lượng lý thuyết
     * @param float $actualQuantity Số lượng thực tế
     * @return array
     */
    public function calculateCheck($theoryQuantity, $actualQuantity) {
        $difference = $actualQuantity - $theoryQuantity;
        
        // Tính trạng thái dựa trên % chênh lệch
        $status = $this->calculateStatus($theoryQuantity, $actualQuantity);
        
        // Tính % chênh lệch
        $percentDifference = 0;
        if ($theoryQuantity != 0) {
            $percentDifference = abs(($difference / $theoryQuantity) * 100);
        }
        
        // Tạo ghi chú tự động
        $note = $this->generateNote($status);

        return [
            'difference' => $difference,
            'status' => $status,
            'percentDifference' => round($percentDifference, 2),
            'note' => $note
        ];
    }

    /**
     * Tính trạng thái dựa trên % chênh lệch
     * @param float $theoryQuantity Số lượng lý thuyết
     * @param float $actualQuantity Số lượng thực tế
     * @return string OK | WARNING | CRITICAL
     */
    public function calculateStatus($theoryQuantity, $actualQuantity) {
        if ($theoryQuantity == 0) {
            return "OK";
        }

        $difference = $actualQuantity - $theoryQuantity;
        $percentDifference = abs(($difference / $theoryQuantity) * 100);

        if ($percentDifference >= 1 && $percentDifference <= 2) {
            return "OK";
        } else if ($percentDifference > 2 && $percentDifference <= 5) {
            return "WARNING";
        } else if ($percentDifference > 5) {
            return "CRITICAL";
        } else {
            return "OK"; // < 1% cũng coi là OK
        }
    }

    /**
     * Tạo ghi chú tự động dựa trên trạng thái
     * @param string $status
     * @return string
     */
    private function generateNote($status) {
        switch ($status) {
            case "OK":
                return "Hợp lệ";
            case "WARNING":
                return "Cảnh báo";
            case "CRITICAL":
                return "Nghiêm trọng";
            default:
                return "";
        }
    }

    /**
     * Validate dữ liệu kiểm kho
     * @param array $data
     * @throws Exception
     */
    private function validateInventoryCheckData($data) {
        // Kiểm tra nguyên liệu
        if (empty($data['ingredient'])) {
            throw new Exception("Tên nguyên liệu không được để trống");
        }

        // Kiểm tra số lượng lý thuyết
        if (!isset($data['theoryQuantity']) || !is_numeric($data['theoryQuantity'])) {
            throw new Exception("Số lượng lý thuyết không hợp lệ");
        }

        // Kiểm tra số lượng thực tế
        if (!isset($data['actualQuantity']) || !is_numeric($data['actualQuantity'])) {
            throw new Exception("Số lượng thực tế không hợp lệ");
        }

        // Số lượng không được âm
        if ($data['theoryQuantity'] < 0 || $data['actualQuantity'] < 0) {
            throw new Exception("Số lượng không được âm");
        }
    }



    /**
     * Lấy báo cáo thất thoát theo khoảng thời gian (từ ngày - đến ngày)
     * @param string $fromDate Ngày bắt đầu (format: Y-m-d)
     * @param string $toDate Ngày kết thúc (format: Y-m-d)
     * @return array
     */
    public function getInventoryCheckByDateRange($fromDate, $toDate) {
        // Validate input
        if (empty($fromDate) || empty($toDate)) {
            throw new Exception("Ngày bắt đầu và ngày kết thúc không được để trống");
        }

        // Validate date format
        $fromDateTime = DateTime::createFromFormat('Y-m-d', $fromDate);
        $toDateTime = DateTime::createFromFormat('Y-m-d', $toDate);

        if (!$fromDateTime || !$toDateTime) {
            throw new Exception("Định dạng ngày không hợp lệ (Y-m-d)");
        }

        if ($fromDateTime > $toDateTime) {
            throw new Exception("Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc");
        }

        return $this->inventoryCheckRepo->getInventoryCheckByDateRange($fromDate, $toDate);
    }
}
?>