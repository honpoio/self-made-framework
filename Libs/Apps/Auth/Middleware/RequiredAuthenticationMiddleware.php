<?php

namespace Libs\Apps\Auth\Middleware;


use Libs\Https\Request;
use Libs\Https\Response;
use Libs\Middleware\BaseMiddleware;
use Libs\Apps\Auth\Services\AuthService;

class RequiredAuthenticationMiddleware extends BaseMiddleware
{
    /* 
    ログイン後、ログイン前のアクセスを制御する
    */
    public static array $IGNORE_URL_PATTERNS = ['/^auth\/(login|sign-up)$/'];
    //loginしなくてもみることができるパスを設定
    private array $_ignore_url_patterns;

    public function __construct()
    {
        $this->_ignore_url_patterns = self::$IGNORE_URL_PATTERNS;
    }

    public function processRequest(Request $request)
    {
        if (AuthService::isAuthenticated()){
            //true(セッションが存在している時はなにもせずに返す)※ログイン時ってこと
            return $request;

        }
        //sessionが作成されてない場合はfalse
        foreach ($this->_ignore_url_patterns as $ignore_url_pattern) {
            //
            if (preg_match($ignore_url_pattern, $request->pathInfo()))
            //login,sign-upページのみ閲覧可能にする
                return $request;
        }
        return Response::redirect('/auth/login');
        //ログイン前でlogin,sign-upページ以外のページを
        //閲覧しようとしている場合は強制的にログイン画面に移行させる 
    }
    
}