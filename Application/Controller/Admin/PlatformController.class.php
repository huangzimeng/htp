<?php

/**
 * 平台统一控制器 需要验证的控制器 都需要继承该控制器
 */
class PlatformController extends Controller
{
    //构造方法
    public function __construct()
    {
        //开启session 检测session中是否有用户信息
        @session_start();
        if (!isset($_SESSION['INFO']) || $_SESSION['INFO']['member_id'] < 0) {     //没有用户信息
            //检测cookie中是否有id 与 pass
            if (isset($_COOKIE['id']) && isset($_COOKIE['pass'])) {
                //根据 id 查询数据
                $memberModel = new MemberModel();
                $result = $memberModel->checkIdPwd($_COOKIE['id'], $_COOKIE['pass']);
                if ($result === false) {
                    $this->redirect("index.php?p=Admin&c=Login&a=index", '未登录', 2);
                }
                //将数据保存到session中
                $_SESSION['INFO'] = $result;
                //跳转到首页
                $this->redirect("index.php?p=Admin&c=Index&a=index");
            }
            $this->redirect("index.php?p=Admin&c=Login&a=index", '未登录', 2);

        }

    }

}