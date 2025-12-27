<?php
class RolesRepository extends ConnectDatabase {
    /**
     * Lấy tất cả roles dưới dạng mảng id => name
     * @return array
     */
    public function findAll() {
        $sql = "SELECT id, role_name, Description FROM roles ORDER BY id";
        $result = mysqli_query($this->con, $sql);

        $roles = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                // ưu tiên role_name, fallback sang các cột khác nếu cần
                $name = $row['role_name'] ?? ($row['name'] ?? ($row['roleName'] ?? ''));
                $roles[(int)$row['id']] = $name;
            }
        }

        return $roles;
    }
}
