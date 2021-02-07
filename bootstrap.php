<?php
// ここでリクエストが来たときのプロジェクトの設定の反映や準備を行います。。
require_once 'Config/DirectorySettings.php';
require_once 'Libs/Utils/AutoLoader.php';
require_once 'Config/ProjectSettings.php';
use Config\DirectorySettings;
use Libs\Utils\AutoLoader;
use Config\ProjectSettings;


if (ProjectSettings::IS_DEBUG)
    ini_set('display_errors', 'On');
    #ini_set -設定オプションの値を設定する


$auto_loader = new AutoLoader(DirectorySettings::APPLICATION_ROOT_DIR);

#$auto_loaderインスタンスを作成

$auto_loader->run();
//


foreach (ProjectSettings::APPLICATIONS as $APPLICATION)
        // ProjectSettings.phpのプロパティ定数APPLICATIONSにアクセス::ConfigApplication.phpから配列を取得j
{
    $application = "";
    $application = new $APPLICATION();
    $application->ready();
}


$project = \Libs\Project::instance();
