<?php


namespace Libs\Https;


class Session
{
    /* 
    Sessionの開始、設定をするクラス
    */

    private static Session $_instance;
    //Sessionクラスのオブジェクトを格納するプロパティ
    private static bool $is_session_started = false;
    //Sessionが開始した時の情報を格納する
    private static bool $is_session_id_regenerated = false;
    //sessionの状態を格納するプロパティ

    private function __construct()
    {
        if (self::$is_session_started) return;

        $this->startSession();
    }

    public static function instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function set($key, $value)
    {
        //trueもしくはfalseに値を変更
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public function unSet($key)
    {
        unset($_SESSION[$key]);
    }

    public function clear()
    {
        $_SESSION = array();
    }

    public function regenerate($destroy = true)
    {
        if (self::$is_session_id_regenerated) return;

        session_regenerate_id($destroy);
        //session_regenerate_id -セッションの再生成
        self::$is_session_id_regenerated = true;
    }

    private function startSession(): void
    //メソッド　:void　--返り値に意味がないことを表す
    {
        session_start();
        //セッションを作成
        //https://qiita.com/7968/items/ce03feb17c8eaa6e4672<=ここがめっちゃわかりやすい
        self::$is_session_started = true;
    }
}