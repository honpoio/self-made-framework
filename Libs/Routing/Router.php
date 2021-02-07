<?php
namespace Libs\Routing;


use Libs\Https\Request;

class Router
/* 
HTTPレスポンスから受け取った値やルーティングで設定した値を元に操作するクラス
*/
{
    private array $routingTables = [];
    //Router,RoutingTableクラスのオブジェクト($urlPatterns)を格納
    //格納されるもの↓
    //>>正規表現
    //>>controllerまでのパス(TaskApp\Controllers\TasksController)
    //>>htmlが記述されているカレントディレクトリ名
    //>>動的パス



    public function __construct($routingTableClasses=[])
    {
        foreach ($routingTableClasses as $routingTableClass){

            $this->add($routingTableClass[0], new $routingTableClass[1]);
        //RoutingTableクラスの連想配列routingTableで設定した
        //app名を全てaddメソッドでセットできるまでループする
        }
    }

    public function add($prefixPregPattern, RoutingTable $routingTable)
    //RoutingTableクラスの連想配列$urlPatternsを引数にする(オブジェクト型)
    {
        $routingTable->registerMyUrlPatterns();
        $this->routingTables[] = [
            'prefixPregPattern' => $prefixPregPattern,
            'table' => $routingTable];
    }

    public function resolve(Request $request){
        //引数にしたHTTPリクエスト情報を使用しcontroller,URLが間違えないか選定する
        $path_info = $request->pathInfo();
        //URLの正規表現

        $result = null;
        foreach ($this->routingTables as $routingTable){
            if (preg_match($routingTable['prefixPregPattern'], $path_info, $matches)){
                //HTTPリクエストメソッドと同じメソッドが見つかるまでループ
                //preg_match -正規表現
                //$matches -第３引数を指定すると検索結果が代入される
                //>(正規表現を()で囲むと元の文字列、マッチした文字列が配列で返される)
                $current_path_info = substr($path_info, strlen($matches[0]));
                //ルートディレクトリを除いたカレントディレクトリを返す
                $result = $routingTable['table']->resolve($current_path_info, $request->methodType());
                //$request->methodType() - HTTPリクエストメソッドを取得
                break;
            }
        }
        return $result;
    }
}