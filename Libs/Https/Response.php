<?php
namespace Libs\Https;


class Response
{
    /* 
    HTTPレスポンスをクライアントに渡すための操作をするクラス
    */
    private  $content;
    //HTML(データ本体)を格納
    private  $status_code;
    //200 レスポンスを格納
    private  $status_text;
    //レスポンス状態を格納
    private  $http_headers;
     //レスポンスヘッダーを格納

    /**
     * Response constructor.
     * @param string $content
     * @param int $status_code
     * @param string $status_text
     */
    public function __construct(
        string $content = "",
        // 指定したコメントが入る
        int $status_code = Status::HTTP_200_OK,
        
        string $status_text =  '')
    {
        $this->content = $content;
        $this->status_code = $status_code;
        $this->status_text = empty($status_text) ? Status::text($status_code) : $status_text;
        $this->http_headers = array();
    }

    /**
     * Send response with header and content.
     */
    public function send()
    {
        header('HTTP/1.1 ' . $this->status_code . ' ' . $this->status_text);
        foreach ($this->http_headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $this->content;
    }

    public function setHttpHeaders($name, $value)
    {
        $this->http_headers[$name] = $value;
    }

    public function statusCode()
    {
        return $this->status_code;
    }

    public function statusText()
    {
        return $this->status_text;
    }

    public static function redirect($location){
        $response = new self("",
            Status::HTTP_301_MOVED_PERMANENTLY);
        $response->setHttpHeaders('Location', $location);
        return $response;
    }
}