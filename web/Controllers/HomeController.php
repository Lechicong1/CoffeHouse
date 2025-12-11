<?php
namespace web\Controllers;

use Config\Controller;

class HomeController extends Controller {

    public function index() {
        // Redirect về file HTML tĩnh để CSS/JS load đúng
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        header("Location: $baseUrl/../web/Views/UserDashBoard/index.html");
        exit;
    }
}
