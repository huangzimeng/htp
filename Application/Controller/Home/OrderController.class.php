<?php

//预约控制器
class OrderController extends PlatformController
{
    //添加预约
    public function add(){
        //>>1.接受数据
        $data = $_POST;
        //>>2.处理数据
        $orderModel = new OrderModel();
        $rs = $orderModel->Add($data);
        //>>3.显示页面
        if ($rs === false){//失败
            self::redirect('index.php?p=Home&c=Index&a=index','预约失败!'.$orderModel->getError(),2);
        }
        //成功
        self::redirect('index.php?p=Home&c=Index&a=index','预约成功!',2);
    }

}