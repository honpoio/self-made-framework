<?php
namespace Libs;


use Config\ProjectSettings;
use Libs\Controllers\Controller;
use Libs\Https\Request;
use Libs\Routing\Router;
use Libs\DB\DBManager;
use Libs\Middleware\MiddlewareManager;
use Libs\Https\Response;
/**
 * Class Project
 * @package Libs
 */

class Project
{
    /*
        MVCを操作するクラス
    */
    private static Project $_instance;
    //Projectクラスのオブジェクトを格納する
    private Request $_request;
    //Requestクラスのオブジェクトが格納されているプロパティ
    private Router $_router;
    //Routingの情報やurlのファイル名の正規表現が格納されているプロパティ
    private MiddlewareManager $middleware_manager;
    //アクセス制御を担当するMiddlewareクラスのオブジェクトが格納されているプロパティ

    private function __construct()
    {
        $this->_request = Request::instance();
        //HTTPリクエストヘッダを取得、Requestクラスのオブジェクトを格納
        $this->_router = new Router(ProjectSettings::ROUTING_TABLE_CLASSES);
        //$urlPatternsで登録したHTTPメソッド名によって連想配列を作成しroutingを格納
        DBManager::instance();
        //DBManagerクラスの$instanceプロパティにPDO情報を格納する
        $this->middleware_manager = new MiddlewareManager(ProjectSettings::MIDDLEWARE_CLASSES);
        //各authのインスタンスを作成
    }

    public static function instance()
    //Projectクラスのオブジェクトを$_instanceに格納
    {
        if (empty(self::$_instance)) {
            //empty -変数の値が0あるいは空、NULLである場合はTRUE
            self::$_instance = new Project();
        }
        return self::$_instance;
    }

    public function run()
    //HTTPリクエストから得た情報でHTTPレスポンスを作成し出力するメソッド
    {
        $result = $this->middleware_manager->processRequest($this->_request);
        //sessionが存在するかどうかチェック,ユーザーの情報を取得
        if ($result instanceof Response) {
            //instanceof (オブジェクトが格納されている変数, クラス名)
            $result->send();
            
            
            return;
        }

        list($controller, $action, $params) = $this->_selectController();
        $response = $this->_actionController($controller, $action, $params);
        $response = $this->middleware_manager->processResponse($response);
        $response->send();
        //HTTPレスポンスをクライアントに渡す
    }

    private function _selectController()
    //HTTPリクエストから得たHTTPメソッド、URLから使用するコントローラを選定するメソッド
    {
        $result = $this->_router->resolve($this->_request);
        if (is_null($result)){
            //パスがない場合notfoundを返す様にする

            $controller = ProjectSettings::NOT_FOUND_CONTROLLER;
            return [new $controller(), 'index', []];
        }
        return [new $result['class'], $result['action'], $result['params']];
    }

    private function _actionController(Controller $controller, string $action, array $params)
    //使用するコントローラーの継承元contorollerクラスのメソッドrunを実行
    {
        return $controller->run($action, $params);
    }
}