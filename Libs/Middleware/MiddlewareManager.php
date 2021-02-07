<?php


namespace Libs\Middleware;


use Libs\Https\Response;

class MiddlewareManager
/* 
ミドルウェアの各種操作などが記載されている
*/
{
    private array $_middleware_list = [];
    //MIDDLEWARE_CLASSESプロパティにて格納したパス先のクラスを格納

    public function __construct($middleware_list)
    {
        foreach ($middleware_list as $middleware) {
            $this->_middleware_list[] = new $middleware;
            //MIDDLEWARE_CLASSESプロパティにて格納した
            //各Middlewareクラスのインスタンスを作成、配列に格納
        }
    }

    public function processRequest($request)
    {
        $result = $request;
        foreach ($this->_middleware_list as $middleware) {
            $result = $middleware->processRequest($result);
            if ($result instanceof Response)
            //sessionを作成してない場合はbreakで抜ける
                break;
        }

        return $result;
    }

    public function processResponse($response) : Response
    {
        $result = $response;
        foreach (array_reverse($this->_middleware_list) as $middleware) {
            $result = $middleware->processResponse($result);
            if ($middleware->haveToReturnResponse())
                break;
        }

        return $result;
    }

}