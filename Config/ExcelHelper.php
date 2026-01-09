<?php
class ExcelHelper {
    /**
     * Xuất dữ liệu ra file Excel (định dạng HTML/XLS)
     *
     * @param mysqli_result|array $data - Dữ liệu từ database hoặc array
     * @param array $headers - Mảng tiêu đề cột ['field' => 'label']
     * @param string $filename - Tên file (không cần extension)
     */
    public static function exportToExcel($data, $headers, $filename = 'DanhSach') {
        // Xóa tất cả output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        $filename = $filename . "_" . date('Y-m-d') . ".xls";

        // Set headers để tải file
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tạo HTML table (Excel có thể mở được)
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '</head>';
        echo '<body>';
        echo '<table border="1">';
        echo '<tr style="background-color: #00FF00; font-weight: bold;">';

        // In tiêu đề
        foreach($headers as $header) {
            echo '<th>' . $header . '</th>';
        }
        echo '</tr>';

        // Kiểm tra kiểu dữ liệu và xử lý phù hợp
        if (is_array($data)) {
            // Nếu là array thông thường
            foreach($data as $row) {
                echo '<tr>';
                foreach(array_keys($headers) as $field) {
                    echo '<td>' . (isset($row[$field]) ? $row[$field] : '') . '</td>';
                }
                echo '</tr>';
            }
        } else {
            // Nếu là mysqli_result
            while($row = mysqli_fetch_array($data)) {
                echo '<tr>';
                foreach(array_keys($headers) as $field) {
                    echo '<td>' . (isset($row[$field]) ? $row[$field] : '') . '</td>';
                }
                echo '</tr>';
            }
        }

        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit;
    }
}
?>
