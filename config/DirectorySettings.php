<?php
namespace Config;

class DirectorySettings
/* 
テンプレートのパス、
controllerのパスを設定するクラス
*/
{
    public const APPLICATION_ROOT_DIR = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
    public const TEMPLATES_ROOT_DIR = self::APPLICATION_ROOT_DIR .DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR;
    #__DIR__ -そのファイル(DirectorySettings)の存在するディレクトリ
    #DIRECTORY_SEPARATOR -パス内のディレクトリを区切る定数
    
}