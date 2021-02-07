<?php
namespace Libs\Controllers;


use Libs\Https\Response;
use Libs\Https\Status;

class NotFoundController extends Controller
/* 
notfound返す時の設定が乗ってるクラス
*/
{
    private string $message;

    public function __construct($message='Page not found.')
    {
        parent::__construct();
        $this->message = $message;
    }

    public function index($params)
    {
        return new Response($this->message, Status::HTTP_404_NOT_FOUND);
    }

}