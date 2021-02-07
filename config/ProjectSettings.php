<?php
namespace Config;


use Libs\Controllers\NotFoundController;
use TaskApp\TaskApplication;
use Libs\Apps\Auth\AuthApplication;

class ProjectSettings
{
    /* 
        各コントローラやミドルウェアなどの情報を格納するクラス
    */
    public const IS_DEBUG = true;
    public const APPLICATIONS = [
        //おそらくbootstrapで読み込むためのクラスを書いている
        ConfigApplication::class,
        AuthApplication::class,
        TaskApplication::class
    ];

    public const MIDDLEWARE_CLASSES = [
        \Libs\Apps\Auth\Middleware\AuthMiddleware::class,
        \Libs\Apps\Auth\Middleware\RequiredAuthenticationMiddleware::class,
    ];

    public const ROUTING_TABLE_CLASSES = [
        //ディレクトリ名の正規表現,routingの情報が格納されているクラスまでのパスを格納
        ['/^tasks(\/|)/', \TaskApp\RoutingTable::class],
        ['/^auth(\/|)/', \Libs\Apps\Auth\RoutingTable::class],
        //　/^tasks(\/|)/  正規表現,tasksかtasks/
        //正規表現の解説 - https://www.webdesignleaves.com/pr/php/php_basic_03.php
    ];


    public const NOT_FOUND_CONTROLLER = NotFoundController::class;
}