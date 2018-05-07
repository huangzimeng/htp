<?php

/**
 * 登录控制器
 */
class LoginController extends Controller
{
    //显示登录界面
    public function index()
    {
        //显示页面
        $this->display();
    }

    //检测登录信息
    public function check()
    {
        //接收参数
        $data = $_POST;
        //处理数据
        $memberModel = new MemberModel();
        $result = $memberModel->check($data);
        //显示页面
        if ($result === false) {
            $this->redirect("index.php?p=Admin&c=Login&a=index", $memberModel->getError(), 2);
        }
        $this->redirect("index.php?p=Admin&a=Index&c=index", '登陆成功', 2);
    }

    //退出
    public function logout()
    {
        //接收参数
        //处理数据
        //开启session 删除session中的数据
        @session_start();
        unset($_SESSION['INFO']);
        unset($_SESSION['CAPTCHA']);
        //删除cookie
        setcookie('id', null, -1, '/');
        setcookie('pass', null, -1, '/');
        //显示页面
        $this->redirect("index.php?p=Admin&c=Login&a=index", '注销中...', 2);
    }


}