<?php
namespace Libs\DB;

abstract class Entity
/* 
抽象クラス
サブクラスが必ず実装すべきメソッドなどを指示するのに使う
*/
{
    public string $id;
    public abstract static function columns();
}