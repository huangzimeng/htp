<?php

/**
 * 前台 活动控制器
 */
class ActivityController extends Controller
{
    //显示活动详情
    public function index(){
        //接收参数
        $id = $_GET['id'];
        //处理数据
        $activityModel = new ActivityModel();
        $activityinfo = $activityModel->getOne($id);
        //显示页面
            //分配数据
        $this->assign($activityinfo);
        $this->display();
    }

}