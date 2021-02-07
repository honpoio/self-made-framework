<?php
namespace Libs\Apps\Auth\Middleware;


use Libs\Https\Request;
use Libs\Middleware\BaseMiddleware;
use Libs\Apps\Auth\Services\AuthService;

class AuthMiddleware extends BaseMiddleware
/*
ログインの有無の情報を取得するクラス
 */
{
    public function processRequest(Request $request){
        $request->user = AuthService::getLoginUser();
        //ユーザーの情報を取得
        return $request;
    }
}