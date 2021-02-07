<?php
namespace Libs\Https;


class Request
{
    /* 
    pathを受け取ったり正規表現でサブディレクトリにしたりするクラス
    */
    private static $instance;
    //Requestクラスのオブジェクトが格納されている
    private array $headers;
    //HTTPリクエストヘッダを格納しているプロパティ
    

    private function __construct()
    {
        $this->headers = getallheaders();
        //getallheadersはどうやらApacheサーバのみしか利用できないっぽい
        // getallheaders — 全てのHTTPリクエストヘッダを取得する
    }

    public static function instance()
    {
        if (empty(self::$instance)) {
            self::$instance = new Request();
        }

        return self::$instance;
    }

    public function methodType(): string
    {
        if (is_null($this->post('_method')))
        // is_null - nullの場合trueを返す
            
            return $_SERVER['REQUEST_METHOD'];
            //$_SERVER['REQUEST_METHOD'] - ページがリクエストされたときのリクエストメソッド名を返す
        return $this->post('_method');
    }

    public function get(string $name, $default = null)
    {
        if (isset($_GET[$name]))
            return $_GET[$name];
        return $default;
    }

    public function post($name, $default = null)
    {
        if (isset($_POST[$name]))
        //isset -null以外trueを返す
            return $_POST[$name];
            //$_POST -POSTで送信したデータを取得する
        
        return $default;
    }

    /**
     * @param null $name
     * @return array | string
     */
    // public function header($name = null)
    // {
        
    //     if (empty($name))
    //     {

        
    //     // empty- 値がnullやfalseの時trueを返す、文字列や数値がある場合はfalseを返す。
    //         return getallheaders();
    //     }
        
    //     return empty($this->headers[$name]) ? '' : $this->headers[$name];
    // }

    // public function host(): string
    // {
    //     if (!empty($_SERVER['HTTP_HOST']))
    //         return $_SERVER['HTTP_HOST'];
    //     return $_SERVER['SERVER_NAME'];
    // }

    public function requestUri(): string
    {
        //HTTPリクエストからホスト名以下のパスを取得
        return $_SERVER['REQUEST_URI'];
        //$_SERVER['REQUEST_URI']　-ホスト名以下のパスを取得
    }

    public function baseUrl(): string
    {
        /*
        ホスト名以下のパスを取得しルートディレクトリに相違がないか確認するメソッド
        */
        $script_name = $_SERVER['SCRIPT_NAME'];
        //$_SERVER['SCRIPT_NAME'] -現在のディレクトリのパス。
        $request_uri = $this->requestUri();
        if (0 === strpos($request_uri, $script_name))
        {
        //strpos -(対象文字列,検索文字列(先頭から数えて検索文字列が一つでも一致しない所があればfalse))
            return $script_name;
        }
        else if (0 === strpos($request_uri, dirname($script_name)))
        //dirname --(対象ディレクトリ ,戻りたい階層の数値を指定(特に無いのであれば親ディレクトリを返す))
            return rtrim(dirname($script_name));
        
        return '';
    }

    public function pathInfo(): string
    {
        /*
        ホスト名以下のパスにクエリ文字列がないかどうか探索、
        見つかった場合はクエリ文字列以降のパスを返す
        見つからない場合は全て返す
        */
        $base_url = $this->baseUrl();
        $request_uri = $this->requestUri();
        $pos = strpos($request_uri, '?');
        // strpos(対象文字列 , 検索する文字列)
        if (false !== $pos)
        //クエリ文字列の?が走査しても見つからなかった場合
            $request_uri = substr($request_uri, 0, $pos);
            //substr- (対象文字列, 対象文字列の頭から対象にする文字列の数,出力する文字列の数)
            //substr($request_uri, 0, $pos)　- 0文字目~走査範囲まで返す
        $path_info = (string)substr($request_uri, strlen($base_url));
        //$request_uriのパス先を$base_url~　?(クエリ文字列)　まで取得
        
        return $path_info;

    }

}