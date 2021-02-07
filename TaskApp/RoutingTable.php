<?php
namespace TaskApp;


use TaskApp\Controllers\TasksController;

class RoutingTable extends \Libs\Routing\RoutingTable
{
    /* 
    サブディレクトリ、
    メソッド、
    controllerまでのパス、
    テンプレート名を格納
    */
    protected array $urlPatterns = [
        ['', 'GET', TasksController::class, 'index'],
        ['int:id', 'GET', TasksController::class, 'detail'],
        ['', 'POST', TasksController::class, 'store'],
        ['int:id/edit', 'GET', TasksController::class, 'edit'],
        ['create', 'GET', TasksController::class, 'create'],
        ['int:id', 'PUT', TasksController::class, 'update'],
        ['int:id', 'DELETE', TasksController::class, 'delete'],
    ];
}