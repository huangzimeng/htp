<?php

/**
 * 前台 订单 控制器
 */
class OrderFormController extends Controller
{
    //显示订单列表
    public function index(){
        //接收参数
        $search = [];   //用来装 搜素条件
        if (!empty($_REQUEST['keyword'])){
            $search[] = "`number` LIKE '%{$_REQUEST['keyword']}%'";
        }
        //
        $page = $_REQUEST['page']??1;
        //处理数据
        $orderformModel = new OrderFormModel();
        $orderformListResult = $orderformModel->getpage($search,$page);
        //利用http_build_query 拼接 链接
        unset($_REQUEST['page']);
        $url_params = http_build_query($_REQUEST);
        //调用分页工具
        $pageHtml = PageTool::show($orderformListResult['count'], $orderformListResult['totalpage'], $orderformListResult['pagesize'], $orderformListResult['page'], $url_params);

        //显示页面
        //分配
        $this->assign('orderformList',$orderformListResult['list']);
        $this->assign('pageHtml',$pageHtml);
        $this->display();
    }

    //查看订单详情
    public function view(){
        //接收参数
        $id = $_GET['id'];
        //处理数据
        $orderformModel = new OrderFormModel();
        $list = $orderformModel->getOne($id);
        //显示页面
        $this->assign($list);
        $this->display();
    }

    //前台订单删除
    public function delete(){
        //接收参数
        $id = $_GET['id'];
        //处理数据
        $orderformModel = new OrderFormModel();
        $result = $orderformModel->delete($id);
        //显示页面
        if ($result === false){
            $this->redirect("index.php?p=Home&c=OrderForm&a=index",$orderformModel->getError(),2);
        }
        $this->redirect("index.php?p=Home&c=OrderForm&a=index",'删除成功!',2);
    }

    //前台 取消订单
    public function cancel(){
        //接收参数
        $id = $_GET['id'];
        //处理数据
        $orderformModel = new OrderFormModel();
        $result = $orderformModel->homecancel($id);
        //显示页面
        if ($result === false){
            $this->redirect("index.php?p=Home&c=OrderForm&a=index",$orderformModel->getError(),2);

        }
        $this->redirect("index.php?p=Home&c=OrderForm&a=index",'处理成功',2);
    }

    //前台显示订单列表
    public function indexHome(){
        //接收参数
        $id = $_GET['id'];
        //接收参数
        $search = [];   //用来装 搜素条件
        if (!empty($_REQUEST['keyword'])){
            $search[] = "`number` LIKE '%{$_REQUEST['keyword']}%'";
        }
        //
        $page = $_REQUEST['page']??1;
        //处理数据
        $orderformModel = new OrderFormModel();
        $orderformListResult = $orderformModel->indexhome($search,$page,$id);
        //利用http_build_query 拼接 链接
        unset($_REQUEST['page']);
        $url_params = http_build_query($_REQUEST);
        //调用分页工具
        $pageHtml = PageTool::show($orderformListResult['count'], $orderformListResult['totalpage'], $orderformListResult['pagesize'], $orderformListResult['page'], $url_params);

        //显示页面
        //分配
        $this->assign('orderformList',$orderformListResult['list']);
        $this->assign('pageHtml',$pageHtml);
        $this->display('index');
    }



}