<?php

/**
 * 前台 商城控制权
 */
class ShopController extends PlatformController
{
    //前台 积分商城 商品购买
    public function buy(){
        @session_start();
        if (empty($_SESSION['USER_INFO'])){
            $this->redirect("index.php?p=Home&c=Index&a=index",'请先登录!',2);
        }
        //接收参数
        $data = $_POST;
        $data ['user_id'] = $_SESSION['USER_INFO']['user_id'];

        //处理数据
        $orderformModel = new OrderFormModel();
        $result = $orderformModel->insert($data);

        //显示页面
        if ($result === false){
            $this->redirect("index.php?p=Home&c=Index&a=index",$orderformModel->getError(),2);
        }
        $this->redirect("index.php?p=Home&c=Index&a=index",'已下单,正在等待处理',2);
    }



}