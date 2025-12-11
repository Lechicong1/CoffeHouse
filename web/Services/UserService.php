<?php
namespace web\Services;

use web\Models\UserEntity;
use web\Repositories\UserRepository;

class UserService {

    private $repo;

    public function __construct() {
        $this->repo = new UserRepository();
    }

    public function getAllUsers() {
        return $this->repo->findAll();
    }

    public function getUserById($id) {
        return $this->repo->findById($id);
    }

    public function register($data) {
        // Validation
        if (empty($data["username"]) || empty($data["password"])) {
            throw new \Exception("Username and password are required");
        }
        
        if (strlen($data["username"]) < 3) {
            throw new \Exception("Username must be at least 3 characters");
        }
        
        if (strlen($data["password"]) < 6) {
            throw new \Exception("Password must be at least 6 characters");
        }
        
        if (!empty($data["email"]) && !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email format");
        }
        
        // Check if username already exists
        $existingUser = $this->repo->findByUsername($data["username"]);
        if ($existingUser) {
            throw new \Exception("Username already exists");
        }
        
        $user = new UserEntity();
        $user->username = trim($data["username"]); // Trim whitespace
        $user->password_hash = password_hash($data["password"], PASSWORD_BCRYPT);
        $user->full_name = $data["full_name"] ?? null;
        $user->email = $data["email"] ?? null;
        $user->phone_number = $data["phone_number"] ?? null;
        $user->address = $data["address"] ?? null;
        $user->role = "customer";
        $user->avatar_url = null;

        return $this->repo->create($user);
    }

    public function updateUser($id, $data) {
        // Kiểm tra user có tồn tại
        $user = $this->repo->findById($id);
        if (!$user) {
            throw new \Exception("User not found");
        }

        // Validation
        if (!empty($data["email"]) && !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email format");
        }

        // Update các field được phép
        if (isset($data['full_name'])) {
            $user->full_name = $data['full_name'];
        }
        if (isset($data['email'])) {
            $user->email = $data['email'];
        }
        if (isset($data['phone_number'])) {
            $user->phone_number = $data['phone_number'];
        }
        if (isset($data['address'])) {
            $user->address = $data['address'];
        }
        
        // Role chỉ được update bởi admin (nên check permission ở đây)
        // Tạm thời cho phép update nhưng nên có middleware check
        if (isset($data['role']) && in_array($data['role'], ['customer', 'staff', 'manager', 'shipper'])) {
            $user->role = $data['role'];
        }

        return $this->repo->update($user);
    }

    public function deleteUser($id) {
        // Kiểm tra user có tồn tại
        $user = $this->repo->findById($id);
        if (!$user) {
            throw new \Exception("User not found");
        }

        return $this->repo->delete($id);
    }
}
?>
