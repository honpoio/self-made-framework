<?php

namespace Libs\Utils;

class AutoLoader
{
    /* 
    ファイルのパスなどを自動で読み込むクラス　requireがいらんくなる
    */
    private $system_root_dir;
    private $applications_root_dir;

    public function __construct(string $root_dir)
    {   
        $this->system_root_dir = $root_dir;
        $this->applications_root_dir = array($this->system_root_dir);
    }

    public function run()
    {                          
        spl_autoload_register(array($this, "loadClass"));
        // コールバック処理　class AutoLoaderのmethod　loadClassに処理をお願いしている
        //bootstrap.phpで呼び出されたクラス以外(未定義)のクラスのパスを引数としloadclass()メソッドを実行
    }

    public function loadClass($class)
    {
        $php_file = $this->create_php_file_path($class);
        if (is_readable($php_file)) {
            require_once $php_file;
            return;
        }
    }

    private function create_php_file_path($class)
    {
        foreach ($this->applications_root_dir as $dir) {
            $pieces = array($dir);
            $class_with_name_space = ltrim($class, '\\');
            //ltrim - (対象文字列, 対象文字列内で削除する文字列);
            $pieces = array_merge($pieces, explode('\\', $class_with_name_space));
            //array_merge -配列を追加　($配列,追加する配列)
            //explode -文字列を文字列により分割する(区切り文字列,　対象文字列)
            $result = implode(DIRECTORY_SEPARATOR, $pieces) . ".php";
            // implode -配列で指定された複数の文字列を連結する
            if (is_readable($result)) return $result;            
        }
        return null;
    }
}