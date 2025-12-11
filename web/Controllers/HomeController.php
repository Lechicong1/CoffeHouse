<?php
namespace web\Controllers;

use Config\Controller;

class HomeController extends Controller {

    public function index() {
        // Redirect to login page or show homepage
        $this->view('home');
    }
}
