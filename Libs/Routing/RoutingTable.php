<?php
namespace Libs\Routing;


class RoutingTable
{
    /* 
    urlPatternsプロパティに登録した情報を整理したり動的パスの情報を取得したりするクラス
    */
    protected array $urlPatterns = [];
    //routingやcontrollerの情報,URLメソッドが格納されている配列
    private array $tables = [];
    //>>controllerまでのディレクトリパス
    //>>htmlが記述されているカレントディレクトリ名

    /**
     * テーブルが空の場合は、全て$this->urlPatternsに登録。
     */
    public function registerMyUrlPatterns()
    {
        /*
        urlPatternsプロパティに登録した 
        URLのパス情報(0),
        HTTPメソッド名(1), 
        Controllerまでのディレクトリパス(2),
        テンプレート名(3)を引数にregisterメソッドにアクセス
        */
        if (count($this->tables) > 0)
        {
        //配列に値が０個以上ある場合はtrue
            return;
        }
        foreach ($this->urlPatterns as $urlPattern) {
            $this->register(
                $urlPattern[0], $urlPattern[1], $urlPattern[2],
                empty($urlPattern[3]) ? 'index' : $urlPattern[3]);
        }
    }
    public function register($pattern, $methodType, $class, $action = 'index')
    {
        /*
        HTTPメソッド名によって連想配列のkeyを登録。
        value(値)にはkeyと同じHTTPメソッドの
        $class(Controllerまでのディレクトリパス),
        $action(テンプレート名)を登録。
        */
        if (empty($this->tables[$methodType])) {
            //empty -変数の値が0あるいは空、NULLである場合はTRUE
            $this->tables[$methodType] = [];
        }
        
        $pieces = explode('/', $pattern);
        //explode -指定した区切り文字によって文字列を分割して配列にする
        $current_pointer = &$this->tables[$methodType];
        //&$this 参照渡し
        foreach ($pieces as $piece) {
            if (empty($current_pointer[$piece])) {
                //empty -変数の値が0あるいは空、NULLである場合はTRUE
                $current_pointer[$piece] = [];
            }
            $current_pointer = &$current_pointer[$piece];
        }
        $current_pointer = [
            'class' => $class,
            'action' => $action
        ];
    }

    /**
     * Resolve controller information like this:
     *
     *   return [
     *    'class' => SomeController::class,
     *    'action' => 'index',
     *    'params' => ['id' => 1]
     *   ]
     *
     * Return null if failed to resolve.
     *
     * @param $pathInfo
     * @param $methodType
     * @return array|null
     */
    public function resolve($pathInfo, $methodType)

    {
        /*
        tablesプロパティに格納されているrouting情報をメソッド事に枝分かれさせる。
        */
        
        if (empty($this->tables[$methodType]))
        //　HTTPリクエストから要求されたメソッド(GET,POST等)が格納されているかどうか確認
        // empty- 値がnullやfalseの時trueを返す、文字列や数値がある場合はfalseを返す。
            return null;
        $params = [];
        $branch = $this->tables[$methodType];
        //リクエストされたメソッドによって分岐
        $pieces = explode('/', $pathInfo);
        // explode -対象文字列を切り分け文字列で配列に順序格納する(切り分け文字列,対象文字列)
        //ヒープ構造みたいな感じでディレクトリごとに枝分かれさせる
        
        for($i = 0; $i < count($pieces); $i++){
            $result = $this->_pickBranch($branch, $pieces[$i], $params);
            // var_dump($result);
            if (is_null($result))
                return null;
                $branch = $result;
                
        }
        if (empty($result['class']) or empty($result['action']))
            return null;

        return ['class' => $result['class'], 'action' => $result['action'], 'params' => $params];
    }

    /**
     * Return null if not found.
     *
     * @param $branch
     * @param $piece
     * @param $params
     * @return mixed|null
     */
    
    private function _pickBranch($branch, $piece, &$params)
    
    {
        /*
        要求されたカレントディレクトリパスと同じルーティングのキーがあるか探索。
        (静的パスの場合)見つかったらリクエストされたcontrollerとtemplateのpathを特定し帰す
        動的パスは_pickParamを確認
        */
        //引数解説↓
        //$branch --resolveメソッドに枝分かれした配列tablesプロパティの情報が格納されてる
        //$piece  --ドメイン以降のカレントディレクトリパス
        //$params --動的パスが格納される
        if (empty($branch[$piece])) {
            
            list($real_piece, $params) = $this->_pickIntParam($branch, $piece, $params);
            //引数が数字であるかどうか検証
            if($real_piece === false){
                list($real_piece, $params) = $this->_pickStrParam($branch, $piece, $params);
                //引数が文字列かどうか検証
                
            }
            if($real_piece === false)
                
                return null;
            
            $piece = $real_piece;
        }
        $result = $branch[$piece];
        //リクエストされたcontrollerとtemplateのpathを特定し帰す
        return $result;
    }


    /**
     * @param $branch
     * @param $piece
     * @param $params
     * @return array
     */
    private function _pickIntParam($branch, $piece, $params)
    //動的パス数値(数字)のメソッド
    {
        
        return $this->_pickParam($branch, $piece, $params, '/^\d+$/', 'int');
        //数値、もしくは数値(数字)の場合trueを返す
    }

    private function _pickStrParam($branch, $piece, $params)
    //動的パス文字列のメソッド
    {
        return $this->_pickParam($branch, $piece, $params, '/^.+$/', 'str');
        // 文字列の場合trueを返す
    }

    /**
     * @param $branch
     * @param $piece
     * @param $params
     * @param $value_pattern
     * @param $value_type
     * @return array
     */
    private function _pickParam($branch, $piece, $params, $value_pattern, $value_type)
    /* 
    routingで設定した動的パスを精査する
    */
    {
        if (preg_match($value_pattern, $piece)) {
            // preg_match(正規表現,文字列)
            foreach (array_keys($branch) as $key) {
                if (preg_match('/' . $value_type . ':(.+)/', $key, $matches)) {
                    //動的パスの正規表現
                    //第３引数を指定すると検索結果が配列で代入される　正規表現で囲むと元の文字列と検索結果が代入される
                    $params[$matches[1]] = $piece;
                    //正規表現の結果を引数にし動的パスを格納
                    $piece = $key;
                    return [$piece, $params];
                    // ドメイン以降のURL,動的パスを格納し帰す
                }
            }
        }
        return [false, false];
    }
}