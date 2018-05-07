<?php

//预约管理控制器
class OrderController extends PlatformController
{
    //预约列表
    public function index(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        //>>1.接受数据
        $page=$_REQUEST['page']??1;
        $search = [];//保存搜索的条件
        if(!empty($_REQUEST['keyword'])){
            $search[] = "(realname like '%{$_REQUEST['keyword']}%' or phone like '%{$_REQUEST['keyword']}%')";
        }
        //>>2.处理数据
        $orderModel = new OrderModel();
        $rows = $orderModel->getAll($page,$search);
        //>>3.显示页面
        //拼接链接的参数 内在函数可以拼接参数
        unset($_REQUEST['page']);
        $url = http_build_query($_REQUEST);
        //>>3.显示页面
        $this->assign('url',$url);
        $this->assign('rows',$rows);
        $this->display('index');
    }
    //处理订单
    public function deal(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);
        //>>1.接受数据
        $order_id = $_GET['order_id'];
        //>>2.处理数据
        $orderModel = new OrderModel();
        $orderModel->Deal($order_id);
        //>>3.显示页面
       self::redirect('index.php?p=Admin&c=Order&a=index');
    }
    //回复订单
    public function reply(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] === "POST"){//回复保存
            //>>1.接受数据
            $data = $_POST;
            //>>2.处理数据
            $orderModel = new OrderModel();
            $rs=$orderModel->Reply($data);
            //>>3.显示页面
            if ($rs === false){
                self::redirect('index.php?p=Admin&c=Order&a=reply&order_id='.$data['order_id'],'回复失败!'.$orderModel->getError(),2);
            }
            self::redirect('index.php?p=Admin&c=Order&a=index','回复成功!',2);
        }else{//回复表单展示
            //>>1.接受数据
            $order_id = $_GET['order_id'];
            //>>2.处理数据
            //>>3.显示页面
            //分配
            $this->assign('order_id',$order_id);
            $this->display('reply');
        }

    }
    //删除订单
    public function delete(){
        //>>1.接受数据
        $order_id = $_GET['order_id'];
        //>>2.处理数据
        $orderModel = new OrderModel();
        $rs = $orderModel->Delete($order_id);
        //>>3.显示页面
        if ($rs === false){
            self::redirect('index.php?p=Admin&c=Order&a=index','删除失败!'.$orderModel->getError(),2);
        }
        self::redirect('index.php?p=Admin&c=Order&a=index','删除成功!',2);
    }

}