<?php
require_once dirname(__DIR__, 2) . '/Config/ConnectDatabase.php';

class ProductReportRepository extends ConnectDatabase
{
    public function getProductReportData($fromDate, $toDate, $categoryId = null)
    {
        $sql = "
            SELECT 
                p.id as product_id,
                p.name as product_name,
                p.image_url,
                c.name as category_name,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.quantity * oi.price_at_purchase) as total_revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN product_sizes ps ON oi.product_size_id = ps.id
            JOIN products p ON ps.product_id = p.id
            JOIN categories c ON p.category_id = c.id
            WHERE 
                o.status = 'COMPLETED'
                AND o.created_at BETWEEN ? AND ?
        ";

        // Add category filter if present
        if ($categoryId && $categoryId !== 'all') {
            $sql .= " AND p.category_id = ? ";
        }

        $sql .= " GROUP BY p.id, p.name, c.name ORDER BY total_revenue DESC";

        $stmt = mysqli_prepare($this->con, $sql);

        if ($stmt) {
            if ($categoryId && $categoryId !== 'all') {
                // sss -> string, string, string (or i for integer id, usually safe to use s or i)
                mysqli_stmt_bind_param($stmt, "sss", $fromDate, $toDate, $categoryId);
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $fromDate, $toDate);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            mysqli_stmt_close($stmt);
            return $data;
        } else {
            // Fallback for debugging
            return [];
        }
    }

    public function getAllCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $result = mysqli_query($this->con, $sql);
        $cats = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $cats[] = $row;
            }
        }
        return $cats;
    }
}
