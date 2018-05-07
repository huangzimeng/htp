<?php

/**
 * 部门管理 控制器
 */
class GroupController extends PlatformController
{
    //列表
    public function index()
    {
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        //接收参数
        $search = [];   //保存搜索数据
        //处理数据
        if (!empty($_REQUEST['keyword'])){
            $search[] = " `name` LIKE '%{$_REQUEST['keyword']}%'";
        }
        //提交页数
        $page = $_GET['page']??1;
        //调用方法
        $groupModel = new GroupModel();
        $groupListResult = $groupModel->getpage($search,$page);
        //利用http_build_query 拼接 链接
        unset($_REQUEST['page']);
        $url_params = http_build_query($_REQUEST);
        //调用分页工具
        $pageHtml = PageTool::show($groupListResult['count'], $groupListResult['totalpage'], $groupListResult['pagesize'], $groupListResult['page'], $url_params);
        //显示列表
            //分配
        $this->assign('groupList',$groupListResult['list']);
        $this->assign('pageHtml',$pageHtml);
        $this->display();
    }

    //添加部门
    public function add(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ( $_SERVER['REQUEST_METHOD'] == "POST" ){
            //接收参数
            $data = $_POST;
            //处理数据
            $groupModel = new GroupModel();
            $result = $groupModel->add($data);
            //显示页面
            if ($result === false){
                $this->redirect("index.php?p=Admin&c=Group&a=add",$groupModel->getError(),2);
            }
            $this->redirect("index.php?p=Admin&c=Group&a=index",'添加成功',2);

        }else{
            //显示列表
            $this->display();
        }
    }

    //修改部门
    public function edit(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        $groupModel = new GroupModel();
        if ($_SERVER['REQUEST_METHOD'] == "POST"){
            //接收参数
            $data = $_POST;
            //处理数据
            $result = $groupModel->editSave($data);
            //显示页面
            if ($result === false){
                $this->redirect("index.php?p=Admin&c=Group&a=edit&id={$data['id']}",$groupModel->getError(),2);
            }
            $this->redirect("index.php?p=Admin&c=Group&a=index",'修改成功',2);

        }else{
            //接收参数
            $id = $_GET['id'];
            //处理数据
            $group = $groupModel->getOne($id);
            var_dump($group);
            //显示页面
                //分配
            $this->assign($group);
            $this->display();
        }
    }

    //删除部门
    public function delete(){
        //接收参数
        $id = $_GET['id'];
        //处理数据
        $groupModel = new GroupModel();
        $result = $groupModel->delete($id);
        //显示页面
        if ($result === false){
            $this->redirect("index.php?p=Admin&c=Group&a=index",$groupModel->getError(),2);
        }
        $this->redirect("index.php?p=Admin&c=Group&a=index",'删除成功',2);
    }

}