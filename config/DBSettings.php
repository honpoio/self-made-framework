<?php
namespace Config;


class DBSettings
/*
DBの設定が格納されているクラス
*/
{
    public const USE_DB = true;
    public const USER = 'sample_app_user';
    public const PASSWORD = 'Pasuwa-do123';
    public const DRIVER = 'mysql';
    public const DB_NAME = "sample_app_db";
    public const HOST = "localhost";
    public const OPTIONS =
        [
            [\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION]
        ];

    public const REPOSITORIES_TABLE =
        [
            [
                'key' => "users",
                'table_name' => 'users',
                "repository" => \Libs\Apps\Auth\Repositories\UsersRepository::class
            ],
            [
                'key' => "tasks",
                'table_name' => 'tasks',
                'entity' => \TaskApp\Entities\Task::class,
                "repository" => \TaskApp\Repositories\TasksRepository::class
            ],
        ];
}