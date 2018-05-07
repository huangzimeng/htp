<?php

//登录控制器
class LoginController extends Controller
{
    //登录表单页
    public function login(){
        //>>1.接受数据
        $data = $_POST;
        //>>2.处理数据
        $userModel = new UserModel();
        $rs = $userModel->check($data);
        //>>3.显示页面
        if ($rs === false){
            self::redirect('index.php?p=Home&c=Register&a=index','登录失败!'.$userModel->getError(),2);
        }
        //保存用户信息到$_SESSION
        @session_start();
        $_SESSION['USER_INFO'] = $rs;
//        判断是否需要记住登录
        if (isset($data['remember'])){//需要记住密码
            setcookie('home_id',$rs['user_id'],time()+7*24*3600,'/');
            setcookie('home_pwd',$rs['password'],time()+7*24*3600,'/');
        }
        self::redirect('index.php?p=Home&c=Index&a=index','登录成功!',2);
    }
    //登录退出
    public function out(){
        //删除用户信息
        @session_start();
        setcookie('home_id',null,-1,'/');
        setcookie('home_pwd',null,-1,'/');
        unset($_SESSION['USER_INFO']);
        self::redirect('index.php?p=Home&c=Index&a=index','您已安全退出!欢迎下次再来!',2);
    }
}