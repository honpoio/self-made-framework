<?php


namespace Libs\Apps\Auth\Controllers;


use Libs\Controllers\Controller;
use Libs\Apps\Auth\Services\AuthService;

class UserController extends Controller
{
    /*
    authページのcontroller
    ログイン、ログアウト、サインインの操作について記載
    */
    public function myPage($params)
    {

        if (AuthService::isAuthenticated() === false){
        //ログインが確認できない場合はloginページへリダイレクト
            return $this->redirect('/auth/login');
        }
    // echo '<pre>';
    // var_dump(\Libs\DB\DBManager::instance()->repository('users')->all());
    // echo '</pre>';
    return $this->render('auth/my_page',['user' => $this->_request->user]);
}

    public function signUpForm($params)
    {
        return $this->render('auth/sign_up');
    }

    public function signUp($params)
    {   
        /*
        HTTPリクエストのPOSTからUser Name、Passwordで入力した値バリデーションチェックし$errorsに格納
        $errorsが空の場合はhttp://localhost:8000/authにリダイレクト
        */
        $errors = AuthService::addNewUser($this->_request->post('name'), $this->_request->post('password'));
        if (count($errors) === 0)
            return $this->redirect('/auth');
        return $this->render('auth/sign_up', ['errors' => $errors]);
    }
    
    public function loginForm($params)
    {
        return $this->render('auth/login');
    }

    public function login($params)
    //ログインする時の操作
    {
        $failed_result = $this->render('auth/login', ['error' => '名前もしくはパスワードが間違えています.']);
        //名前もしくはPWに不備があった場合のためにレスポンス用のデータを$failed_resultに格納
        $user = AuthService::getUser($this->_request->post('name'));
        //postで送られてきたUser Nameの値を検証
        if (is_null($user)){
            return $failed_result;
        }

        if (AuthService::login($user, $this->_request->post('password'))){
        //postで送られてきたpasswordの値を検証
            return $this->redirect('/auth/');
        }
        return $failed_result;
    }

    public function logout($params)
    {
        //ログアウトする時の操作
        AuthService::logout();
        return $this->redirect('/auth/login');
    }
}