<?php

//日志控制器
class LogController extends PlatformController
{
    //列表展示
    public function index(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);
        //>>1.接受数据
        $page=$_REQUEST['page']??1;
        $search = [];//保存搜索的条件
        if(!empty($_REQUEST['keyword'])){
            $search[] = " content like '%{$_REQUEST['keyword']}%'";
        }
        //>>2.处理数据
        $historiesModel = new HistoriesModel();
        $rows = $historiesModel->getAll($page,$search);
        //拼接链接的参数 内在函数可以拼接参数
        unset($_REQUEST['page']);
        $url = http_build_query($_REQUEST);
        //>>3.显示页面
        $this->assign('url',$url);
        $this->assign('rows',$rows);
        $this->display('index');
    }

}