<?php
namespace Libs\Controllers;


use Config\ProjectSettings;
use Libs\Https\Request;
use Libs\Https\Response;
use Libs\views\view;

class Controller
{
    /* 
    ページ遷移やHTMLリクエストを作成するクラス
    */
    /**
     * @var Request
     */
    protected Request $_request;
    //Requestクラスのオブジェクトを格納
    //protected -クラス自身と継承クラスからアクセス可能

    public function __construct()
    {
        $this->_request = Request::instance();
    }
    public function run($action, $params = [])
    // 使用するcontorllerのメソッドを実行
    {
        if (!method_exists($this, $action)) {
            // 指定したメソッドが指定したobjectにおいて定義されている場合にTRUE、そうでない場合にFALSE を返す。
            return $this->render404("Page not found.");
        }

        return $this->$action($params);
    }

    public function render($file_path_after_templates_dir, $data=[])
    //HTTPレスポンスデータの作成,クライアントに反映させるための準備
    {
        $view = new View();
        
        
        return new Response($view->render($file_path_after_templates_dir, $data));
    }

    protected function redirect($uri)
    {
        return Response::redirect($uri);
    }


    protected function render404($message='Page not found.')
    {
        
        $controller = ProjectSettings::NOT_FOUND_CONTROLLER;
        $controller = new $controller($message);
        return $controller->index([]);
    }
}