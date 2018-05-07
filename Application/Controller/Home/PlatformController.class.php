<?php
//前台平台控制器
class PlatformController extends Controller
{
    public function __construct()
    {
        if (isset($_COOKIE['home_id']) && isset($_COOKIE['home_pwd'])){
            //根据id查询数据
            $home_id = $_COOKIE['home_id'];
            $home_pwd = $_COOKIE['home_pwd'];
            $userModel = new UserModel();
            $rs = $userModel->CheckIdpwd($home_id,$home_pwd);
            //验证成功!重新将用户信息保存到session中
            if ($rs !== false){
                @session_start();
                $_SESSION['USER_INFO'] = $rs;
            }
            //跳转到前台首页
            return;
        }
    }
}