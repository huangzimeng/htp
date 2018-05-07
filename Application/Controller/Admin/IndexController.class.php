<?php
//后台首页
class IndexController extends PlatformController
{
    //展示后台首页
    public function index(){
        //>>1.接受数据
        //>>2.处理数据
        @session_start();
        $username = $_SESSION['INFO']['realname'];
        $photo = $_SESSION['INFO']['photo'];
            //获取排行榜信息
        $rankModel = new RankModel();
        $list1 = $rankModel->index1();
        $list2 = $rankModel->index2();
        $list3 = $rankModel->index3();
            //获取注册用户数量
        $userModel= new UserModel();
        $count['count'] = $userModel->count();
            //获取总充值数
        $Allin['Allin'] = $userModel->Allin();
            //获取总消费金额
        $Allout['Allout'] = $userModel->Allout();
        //>>3.显示页面
            //分配
        $this->assign('username',$username);
        $this->assign('photo',$photo);
        $this->assign($count);
        $this->assign($Allin['Allin']);
        $this->assign($Allout['Allout']);
        $this->assign('list1',$list1['result']);
        $this->assign('list2',$list2['result']);
        $this->assign('list3',$list3['result']);
        $this->assign('data1',$list1['data']);
        $this->assign('data2',$list2['data']);
        $this->assign('data3',$list3['data']);
            //显示
        $this->display('index');
    }

    //后台首页展示个人信息
    public function profile(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);
        //
        //
        //
        $this->display();
    }

    //修改个人信息
    public function update(){
        //接收参数
        $data = $_POST;
        $files = $_FILES['photo'];
//        echo '<pre/>';
//        var_dump($data);die;
        //处理数据
        $memberModel = new MemberModel();
        $result = $memberModel->update($data,$files);
        //
        if ($result === false){
            $this->redirect("index.php?p=Admin&c=Index&a=profile",$memberModel->getError(),2);
        }
        $this->redirect("index.php?p=Admin&c=Index&a=profile",'修改成功',2);
    }

}