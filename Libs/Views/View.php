<?php
namespace Libs\Views;

use Config\DirectorySettings;

class View
{
    protected array $_defaultData = [];

    public function __construct()
    {
        $this->_defaultData['escape'] = $this->escape();
    
    }

    public function render($_file_path_after_templates_dir, $_data = array())
    {
        $_file = DirectorySettings::TEMPLATES_ROOT_DIR
            . $_file_path_after_templates_dir . '.tmp.php';
        // var_dump(array_merge($this->_defaultData, $_data));
        extract(array_merge($this->_defaultData, $_data));
        //ここでテンプレートに$tasksを出力している？？？
        //どうやらextractに引数にした連想配列のキーが変数名として出力できるっぽい


        // echoとかの出力をため込む宣言です。
        ob_start();
        // ため込み先のバッファの上限を無効化します。
        ob_implicit_flush(0);
        require $_file;
        // ため込んだ出力を$contentに代入します。
        $content = ob_get_clean();
        
        return $content;
    }

    public function escape()
    {
        return function ($string, $echo = true) {
            
            $value = htmlspecialchars($string, ENT_QUOTES < 'UTF-8');
            //htmlspecialchars -エスケープ処理
            // ENT_QUOTES シングルクオートとダブルクオートをエスケープ処理されない
            //
            if (!$echo)
                return $value;
            echo $value;
        };
    }
}