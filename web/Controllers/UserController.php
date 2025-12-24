<?php
/**
 * UserController - Xử lý các thao tác liên quan đến User
 */
class UserController extends Controller {

    private $service;

    public function __construct() {
        // Include Service cần thiết
        require_once './web/Services/UserService.php';
        $this->service = new UserService();
    }

    public function index() {
        try {
            $users = $this->service->getAllUsers();
            $this->view('user_list', ['users' => $users]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo "Error: " . $e->getMessage();
        }
    }

    public function detail($id) {
        try {
            $user = $this->service->getUserById($id);
            
            if (!$user) {
                http_response_code(404);
                echo "User not found";
                return;
            }
            
            $this->view('user_detail', ['user' => $user]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo "Error: " . $e->getMessage();
        }
    }

    public function registerPage() {
        $this->view('register');
    }

    public function handleRegister() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo "Method not allowed";
                return;
            }
            
            $result = $this->service->register($_POST);
            if ($result) {
                // Redirect về login hoặc homepage
                $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
                header("Location: $baseUrl/../web/Views/Auth/Login/login.html");
                exit;
            }
        } catch (\Exception $e) {
            http_response_code(400);
            echo "Registration failed: " . $e->getMessage();
        }
    }
}
