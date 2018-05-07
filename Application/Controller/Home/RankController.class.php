<?php

/**
 * 前台 排行榜 控制器
 */
class RankController extends PlatformController
{
    //显示排行榜
    public function index(){
        //接收参数
        //处理数据
        $rankModel = new RankModel();
        $list1 = $rankModel->index1();
        $list2 = $rankModel->index2();
        $list3 = $rankModel->index3();
        //显示列表
        $this->assign('list1',$list1);
        $this->assign('list2',$list2);
        $this->assign('list3',$list3);
        $this->display();

    }

}