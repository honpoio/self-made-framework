<?php
namespace Libs\DB;


use Config\DBSettings;

class DBManager
{
    /* 
    mysqlの接続、テーブルやカラムの設定をするクラス
    */
    private static DBManager $instance;
    //DBManagerクラスのオブジェクトを格納する
    private \PDO $connection;
    //pdoのオブジェクトを格納するプロパティ
    private array $repository_table = array();
    //カラム名,テーブル名、pdoのオブジェクト, レポジトリのパス(DBを操作するためのクラス)
    //を格納するプロパティ

    private function __construct()
    {
        $this->initialize();
    }
    public function __destruct()
    {
        foreach ($this->repository_table as $repository){
            unset($repository);
        }
        unset($this->connection);
    }

    public static function instance()
    //PDOオブジェクトのインスタンスを作成するメソッド
    {
        if (empty(self::$instance)) {
            //empty -変数の値が0あるいは空、NULLである場合はTRUE
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $repository_key
     * @return Repository
     */
    public function repository($repository_key)
    // repository_key　--定義したテーブル名
    {
        
        return $this->repository_table[$repository_key];
    }

    public function registerRepositories(array $repository_table)
    {
        $this->repository_table = array_merge($this->repository_table, $repository_table);
        //プロパチーのrepository_tableにカラム名,テーブル名、pdoの情報を格納
    }


    private function initialize()
    /* 
    PDOでmysqlに接続
    */
    {
        $dsn = $this->createDsn();
        //テーブル名、DB言語名、ホスト名を$dsnに格納
        
        $this->connection = new \PDO(
            $dsn,
            DBSettings::USER,
            DBSettings::PASSWORD
        );
        //DBに接続、プロパチーのconnectionに接続
        foreach(DBSettings::OPTIONS as $option){
            $this->connection->setAttribute($option[0], $option[1]);
        }

        $repositories = array();
        foreach(DBSettings::REPOSITORIES_TABLE as $repo_table){
            $repo = new $repo_table['repository'](
                $repo_table['table_name'],
                $this->connection);
                //カラム名,テーブル名、pdoの情報が入ってる
            $repositories[$repo_table['key']] = $repo;
            
        }

        $this->registerRepositories($repositories);
    }

    private function createDsn(): string
    {
        $dsn = DBSettings::DRIVER . ":" .
            "dbname=" . DBSettings::DB_NAME . ";" .
            "host=" . DBSettings::HOST . ";";
        return $dsn;
    }
}