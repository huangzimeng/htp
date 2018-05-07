<?php

/**
 * 后台活动 控制器
 */
class ActivityController extends PlatformController
{
    //列表
    public function index(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        //接收参数
        $search = [];   //用来装 搜素条件
        if (!empty($_REQUEST['keyword'])){
            $search[] = " `title` LIKE '%{$_REQUEST['keyword']}%'";
        }
        //
        $page = $_REQUEST['page']??1;
        //处理数据
        $activityModel = new ActivityModel();
        $activityListResult = $activityModel->getpage($search,$page);
        //利用http_build_query 拼接 链接
        unset($_REQUEST['page']);
        $url_params = http_build_query($_REQUEST);
        //调用分页工具
        $pageHtml = PageTool::show($activityListResult['count'], $activityListResult['totalpage'], $activityListResult['pagesize'], $activityListResult['page'], $url_params);

        //显示页面
            //分配
        $this->assign('activityList',$activityListResult['list']);
        $this->assign('pageHtml',$pageHtml);
        $this->display();
    }

    //添加活动
    public function add(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] == "POST"){
            //接收参数
            $data = $_POST;
            //处理数据
            $activityModel = new ActivityModel();
            $result = $activityModel->insert($data);
            //显示页面
            if ($result === false){
                $this->redirect("index.php?p=Admin&c=Activity&a=add",$activityModel->getError(),2);
            }
            $this->redirect("index.php?p=Admin&c=Activity&a=index",'添加成功!',2);
        }else{
            //接收参数
            //处理数据
            //显示页面
            $this->display();
        }
    }

    //编辑
    public function edit(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        $activityModel = new ActivityModel();
        if ($_SERVER['REQUEST_METHOD'] == "POST"){
            //接收参数
            $data = $_POST;
            //处理数据
            $result = $activityModel->update($data);
            //显示页面
            if ($result === false){
                $this->redirect("index.php?p=Admin&c=Activity&a=edit&id={$data['id']}",$activityModel->getError(),2);
            }
            $this->redirect("index.php?p=Admin&c=Activity&a=index",'修改成功!',2);

        }else{
            //接收参数
            $id = $_GET['id'];
            //处理数据
            $activity = $activityModel->getOne($id);
            //显示页面
                //分配
            $this->assign($activity);
            $this->display();
        }
    }

    //删除活动
    public function delete(){
        //接收参数
        $id = $_GET['id'];
        //处理数据
        $activityModel = new ActivityModel();
        $result = $activityModel->delete($id);
        //显示页面
        if ($result === false){
            $this->redirect("index.php?p=Admin&c=Activity&a=index",$activityModel->getError(),2);
        }
        $this->redirect("index.php?p=Admin&c=Activity&a=index",'删除成功',2);
    }



}