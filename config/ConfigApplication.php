<?php
namespace Config;


use Libs\Application;

class ConfigApplication extends Application
/* 
    よくわからん、、、何がしたいメソッドなんだこれ、、、
*/
{
    public function ready()
    {
        \Libs\Apps\Auth\Middleware\RequiredAuthenticationMiddleware::$IGNORE_URL_PATTERNS[] = '/^tasks\/$/';
    }
}