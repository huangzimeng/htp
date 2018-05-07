<?php

//vip等级 控制器
class VipController extends PlatformController
{
    //vip列表展示
    public function index(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        //>>1.接受数据
        //>>2.处理数据
        $vipModel = new VipModel();
        $rows = $vipModel->getAll();
        //>>3.显示页面
        //分配
        $this->assign('rows',$rows);
        $this->display('index');
    }
    //添加
    public function add(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] === "POST"){//添加保存
            //>>1.接受数据
            $data = $_POST;
            //>>2.处理数据
            $vipModel = new VipModel();
            $rs = $vipModel->Add($data);
            //>>3.显示页面
            if ($rs === false){
                self::redirect('index.php?p=Admin&c=Vip&a=add','添加失败!'.$vipModel->getError(),2);
            }
            self::redirect('index.php?p=Admin&c=Vip&a=index','添加成功!',2);
        }else{//添加列表展示
            //>>1.接受数据
            //>>2.处理数据
            //>>3.显示页面
            $this->display('add');
        }
    }
    //修改
    public function edit(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] === "POST"){//修改保存
            //>>1.接受数据
            $data = $_POST;
            //>>2.处理数据
            $vipModel = new VipModel();
            $rs = $vipModel->EdirSave($data);
            //>>3.显示页面
            if ($rs === false){
                self::redirect('index.php?p=Admin&c=Vip&a=edit&vip_id='.$data['vip_id'],'修改失败!'.$vipModel->getError(),2);
            }
         self::redirect('index.php?p=Admin&c=Vip&a=index','修改成功!',2);
        }else{//修改表单展示
            //>>1.接受数据
            $vip_id = $_GET['vip_id'];
            //>>2.处理数据
            $vipModel = new VipModel();
            $row = $vipModel->Edit($vip_id);
            //>>3.显示页面
            //分配
            $this->assign($row);
            $this->display('edit');
        }
    }
    //删除
    public function delete(){
        //>>1.接受数据
        $vip_id = $_GET['vip_id'];
        //>>2.处理数据
        $vipModel = new VipModel();
        $vipModel->Delete($vip_id);
        //>>3.显示页面
        self::redirect('index.php?p=Admin&c=Vip&a=index','删除成功!',2);
    }

}