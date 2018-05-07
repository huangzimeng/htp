<?php

/**
 * 商城 控制器
 */
class ShopController extends PlatformController
{
    //列表  搜索 + 分页
    public function index(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        //接收参数
        $search = [];   //用来装 搜素条件
        if (!empty($_REQUEST['keyword'])){
            $search[] = " (`shop_name` LIKE '%{$_REQUEST['keyword']}%' OR `money` LIKE '%{$_REQUEST['keyword']}%' OR `intro` LIKE '%{$_REQUEST['keyword']}%')";
        }
        //
        $page = $_REQUEST['page']??1;
        //处理数据
        $shopModel = new ShopModel();
        $shioListResult = $shopModel->getpage($search,$page);
        //利用http_build_query 拼接 链接
        unset($_REQUEST['page']);
        $url_params = http_build_query($_REQUEST);
        //调用分页工具
        $pageHtml = PageTool::show($shioListResult['count'], $shioListResult['totalpage'], $shioListResult['pagesize'], $shioListResult['page'], $url_params);

        //显示页面
        //分配
        $this->assign('shopList',$shioListResult['list']);
        $this->assign('pageHtml',$pageHtml);
        $this->display();

    }

    //添加商品
    public function add(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] == "POST"){
            //接收参数
            $data = $_POST;
            $files = $_FILES['photo'];
            //处理数据
            $shopModel = new ShopModel();
            $result = $shopModel->add($data,$files);
            //显示页面
            if ($result === false){
                $this->redirect("index.php?p=Admin&c=Shop&a=add",$shopModel->getError(),2);
            }
            $this->redirect("index.php?p=Admin&c=Shop&a=index",'添加成功!',2);

        }else{
            //接收参数
            //处理数据
            //显示页面
            $this->display();
        }

    }

    //修改商品
    public function edit(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        $shopModel = new ShopModel();
        if ($_SERVER['REQUEST_METHOD'] == "POST"){
            //接收参数
            $data = $_POST;
            $files = $_FILES['photo'];
            //处理数据
            $result = $shopModel->edit($data,$files);
            //显示页面
            if ($result === false){
                $this->redirect("index.php?p=Admin&c=Shop&a=edit&id={$data['id']}",$shopModel->getError(),2);
            }
            $this->redirect("index.php?p=Admin&c=Shop&a=index",'修改成功!',2);

        }else{
            //接收参数
            $id = $_GET['id'];
            //处理数据
            $row = $shopModel->getOne($id);
            //显示页面
                //分配
            $this->assign($row);
            $this->display();
        }
    }

    //删除商品
    public function delete(){
        //接收参数
        $id = $_GET['id'];
        //处理数据
        $shopModel = new ShopModel();
        $result = $shopModel->delete($id);
        //显示页面
        if ($result === false){
            $this->redirect("index.php?p=Admin&c=Shop&a=index",$shopModel->getError(),2);
        }
        $this->redirect("index.php?p=Admin&c=Shop&a=index",'删除商品成功!',2);
    }


}