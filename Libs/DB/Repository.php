<?php
// PHPからデータベースを操作する手順
// ①データベースに接続する。
// ②実行したいSQL文をセットする。 === prepare
// ③SQLに対してパラメーターをセットする。【任意】 === bindValue
// ④実際にSQLを実行する。 === execute
// ⑤結果を取得する。【任意】 === fetchAll
// ⑥データーベースから切断する。
namespace Libs\DB;


class Repository
{
    /* 
    PHPからデータベースを操作するクラス
    */
    protected string $_table_name;
    //テーブル名が格納されているプロパティ
    protected \PDO $_connection;
    //pdoの情報が格納されているプロパティ

    private string $_entity_class;
    //実行結果が格納されているプロパテl

    /**
     * Override this method to return entity class of repository.
     * @return null
     */
    protected function entityClass()
    {
        return null;
    }

    public function __construct($table_name, \PDO $connection, $entity_class = null)
    {
        $this->_table_name = $table_name;
        $this->_connection = $connection;
        $this->_entity_class = is_null($entity_class) ? $this->entityClass() : $entity_class;
        #var_dump($this->_entity_class);
        if (is_null($this->_entity_class)) {
            throw new \InvalidArgumentException('$this->entity_class is required not string.');
        }
        
    }

    public function all()
    {
        $sql = "select * from {$this->_table_name}";
        return $this->fetchAll($sql);
    }
    
    public function get($id)
    {
        $result = $this->where('id', '=', $id);
        return empty($result[0]) ? null : $result[0];
    }

    public function where($column, $operator, $value)
    {
        $sql = "select * from {$this->_table_name} where {$column} {$operator} :value";
        return $this->fetchAll($sql, [':value' => $value]);
    }

    public function insert(Entity $Entity)
    {
        $columns = implode(', ', $this->_columns());
        $values_columns = implode(', ', $this->_to_params($this->_columns()));
        $sql = "insert into {$this->_table_name} ({$columns}) values ({$values_columns})";
        $params = [];
        foreach ($this->_columns() as $key) {
            $params[$this->_to_param($key)] = $Entity->$key;
        }
        $this->execute($sql, $params);
    }
    
    public function update(Entity $entity)
    {
        $sql = "update {$this->_table_name} set ";
        $params = [];
        foreach ($this->_columns() as $column) {
            $key = $this->_to_param($column);
            $sql .= " {$column} = {$key},";
            $params[$key] = $entity->$column;
        }
        $sql = rtrim($sql, ',');
        $sql .= " where id = :id";
        $params[':id'] = $entity->id;
        $this->execute($sql, $params);
    }

    public function delete($id)
    {
        $sql = "delete from {$this->_table_name} where id = :id";
        //where id = :id" === エスケープ処理
        $this->execute($sql, [':id' => $id]);
    }
    
    private function execute($sql, $params = [])
    {
        $query = $this->_connection->prepare($sql);
        //prepare -- 実行したいSQL文をセットする。
        $query->execute($params);
        return $query;
    }
    
    public function fetchAll($sql, $params = [])
    {
        $query = $this->execute($sql, $params);
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->_entity_class);
    }

    private function _columns(): array
    {
        return $this->_entity_class::columns();
    }

    private function _to_param(string $key): string
    {
        return ':' . $key;
    }

    private function _to_params(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[] = $this->_to_param($key);
        }

        return $result;
    }
}