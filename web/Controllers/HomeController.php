<?php
/**
 * HomeController - Xử lý trang chủ
 */
class HomeController extends Controller {

    /**
     * Action mặc định - Hiển thị trang chủ
     */
    public function index() {
        // Hiển thị trang chủ (index.html trong UserDashBoard)
        $this->view('UserDashBoard/index.html');
    }

    /**
     * Hiển thị trang menu
     */
    public function menu() {
        $this->view('UserDashBoard/menu.html');
    }

    /**
     * Hiển thị trang about
     */
    public function about() {
        $this->view('UserDashBoard/about.html');
    }
}
