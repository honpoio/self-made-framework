<?php


namespace Libs\Middleware;


use Libs\Https\Request;
use Libs\Https\Response;

class BaseMiddleware
{
    protected bool $_have_to_return_response_immediately = false;

    /**
     * リクエスト受け取り何かしらの処理をする関数。
     *   何も問題がなければリクエストを返却
     *  問題が発生し(ログインしてないなど)すぐにレスポンスを返したい場合は、レスポンスを返却するという仕様。
     *  
     *
     * @param Request $request
     * @return Request|Response
     */
    public function processRequest(Request $request){
        return $request;
    }

    /**
     * レスポンスを受け取り何かしらの処理をする関数。
    *  何も問題がなければレスポンスを返却。
     * 何か問題が発生した場合は$_have_to_return_resonse_immediatelyをtrueにしてレスポンスを返却するという仕様。
     * @param Response $response
     * @return Response
     */
    public function processResponse(Response $response){
        return $response;
    }

    public function haveToReturnResponse(){
        return $this->_have_to_return_response_immediately;
    }
}