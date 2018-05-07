<?php

//充值优惠活动控制器
class RuleController extends PlatformController
{
    //列表展示
    public function index(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        //>>1.接受数据
        //>>2.处理数据
        $ruleModel = new RuleModel();
        $rows = $ruleModel->getAll();
        //>>3.显示页面
        //分配
        $this->assign('rows',$rows);
        $this->display('index');
    }
    //添加数据
    public function add(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] === "POST"){//添加保存
            //>>1.接受数据
            $data = $_POST;
            //>>2.处理数据
            $ruleModel = new RuleModel();
            $rs = $ruleModel->Add($data);
            //>>3.显示页面
            if ($rs === false){
                self::redirect('index.php?p=Admin&c=Rule&a=add','添加失败!'.$ruleModel->getError(),2);
            }
            self::redirect('index.php?p=Admin&c=Rule&a=index','添加成功!',2);
        }else{//展示表单
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
            $ruleModel = new RuleModel();
            $rs=$ruleModel->EditSave($data);
        //>>3.显示页面
            if ($rs === false){
                self::redirect('index.php?p=Admin&c=Rule&a=edit&rule_id='.$data['rule_id'],'修改失败!'.$ruleModel->getError(),2);
            }
            self::redirect('index.php?p=Admin&c=Rule&a=index','修改成功!',2);
        }else{//展示表单
        //>>1.接受数据
            $rule_id = $_GET['rule_id'];
        //>>2.处理数据
            $ruleModel = new RuleModel();
            $row = $ruleModel->Edit($rule_id);
        //>>3.显示页面
            //分配
            $this->assign($row);
            $this->display('edit');
        }
    }
    //删除
    public function delete(){
            //>>1.接受数据
        $rule_id = $_GET['rule_id'];
            //>>2.处理数据
        $ruleModel = new RuleModel();
        $ruleModel->Delete($rule_id);
            //>>3.显示页面
        self::redirect('index.php?p=Admin&c=Rule&a=index','删除成功!',2);
    }
}