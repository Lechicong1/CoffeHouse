<?php
/**
 * Class Controller - Base Controller
 * Class cha cho tất cả các Controller trong hệ thống MVC
 */
class Controller {

//    public function model($model) {
//        include_once './web/Models/' . $model . '.php';
//        return new $model;
//    }

    public function service($service) {
        include_once './Config/Service.php';
        include_once './web/Services/' . $service . '.php';
        return new $service;
    }

    public function view($view, $data = []) {
        if (!empty($data)) {
            extract($data);
        }
        include_once './web/Views/' . $view . '.php';
    }
}
?>
