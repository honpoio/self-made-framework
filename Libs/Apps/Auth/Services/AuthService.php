<?php
namespace Libs\Apps\Auth\Services;

use Libs\DB\DBManager;
use Libs\Apps\Auth\Entities\User;
use Libs\Https\Session;

class AuthService
{
    /*
    バリデーションチェックやセッションの管理をするクラス
    */

    const AUTHENTICATED_KEY = '_authenticated';
    //ログインしているかどうか確認
    const AUTH_ID_KEY = '_auth_id';

    protected static function _repository()
    {
        //DBManagerクラス内でusersテーブルのインスタンスを作成
        return DBManager::instance()->repository('users');
    }

    protected static function _session()
    {
        //sessionクラスのオブジェクトを格納
        return Session::instance();
    }

    public static function getLoginUser($is_secure = true)
    //クライアントのログインの有無、情報を取得
    {
        $user =  self::_repository()->get(self::_session()->get(self::AUTH_ID_KEY));
        //DBManagerクラスにアクセス→repository(DBの操作手順があるクラス)アクセス
        //引数Sessionクラスのgetメソッドにアクセス
        if (empty($user))
        //null(sessionに変数が無い場合はnullで返ってくる)
            return $user;
        if($is_secure === false)
            return $user;
        $user->password = '';
        //PWを秘匿化
        return $user;
    }
    

    public static function getUser($name)
    {
        //usersテーブルにアクセスしクライアントが入力した値と一致するかどうか検証
        $result = self::_repository()->where('name', '=', $name);
        return empty($result[0]) ? null : $result[0];
    }

    public static function login(User $user, $password)
    {
        if (password_verify($password, $user->password) === false) {
            //password_verify　--ハッシュがパスワードにマッチするかどうかを調べる
            return false;
        }

        self::_session()->set(self::AUTHENTICATED_KEY, true);
        self::_session()->set(self::AUTH_ID_KEY, $user->id);
        self::_session()->regenerate();
        return true;
    }

    public static function logout()
    {
        //プロパティを全てfalseに設定
        self::_session()->set(self::AUTHENTICATED_KEY, false);
        self::_session()->set(self::AUTH_ID_KEY, false);
        self::_session()->regenerate();
    }

    public static function isAuthenticated()
    //ログインしているかどうか確認するメソッド
    {
        
        return self::_session()->get(self::AUTHENTICATED_KEY) === true;
    }
    

    public static function addNewUser($name, $password)
    {
        /* 
        バリデーションチェックをするメソッド
        */
        $errors= [];

        if (self::_repository()->isUniqueName($name) === false) {
        //User Nameに同じ名前がないか検査(バリデーションチェック)
            $errors['name'] = "'{$name}''は既に登録されているユーザー名です";
        };
        //パスワードが８文字以上か検査
        if (strlen($password) < 8) {
            $errors['password'] = "パスワードは８文字以上にしてください";
        }
        
        if (count($errors) === 0)
            self::_repository()->add($name, $password);
            // エラーが無い場合空の配列を返す
        return $errors;
    }
    

}