<?php
/**
 * Class Service - Base Service
 * Class cha cho tất cả các Service trong hệ thống
 */
class Service {

    protected function repository($repository) {
        include_once './web/Repositories/' . $repository . '.php';
        return new $repository;
    }

    protected function model($model) {
        include_once './web/Models/' . $model . '.php';
        return new $model;
    }
}
?>
