<?php

//套餐控制器
class PlansController extends PlatformController
{
    //列表首页
    public function index(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        //接收参数
        $search = [];   //用来装 搜素条件
        if (!empty($_REQUEST['keyword'])){
            $search[] = "(`name` LIKE '%{$_REQUEST['keyword']}%' OR `money` LIKE '%{$_REQUEST['keyword']}%')";
        }
        //
        $page = $_REQUEST['page']??1;
        //处理数据
        $plansModel = new PlansModel();
        $plansListResult = $plansModel->getpage($search,$page);
        //利用http_build_query 拼接 链接
        unset($_REQUEST['page']);
        $url_params = http_build_query($_REQUEST);
        //调用分页工具
        $pageHtml = PageTool::show($plansListResult['count'], $plansListResult['totalpage'], $plansListResult['pagesize'], $plansListResult['page'], $url_params);

        //显示页面
        //分配
        $this->assign('plansList',$plansListResult['list']);
        $this->assign('pageHtml',$pageHtml);
        $this->display();

    }
    //添加套餐
    public function add(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] === "POST"){//添加保存
            //>>1.接受数据
            $data = $_POST;
            //>>2.处理数据
            $plansModel = new PlansModel();
            $rs = $plansModel->Insert($data);
            //>>3.显示页面
            if ($rs === false){//失败
                self::redirect('index.php?p=Admin&c=Plans&a=add','添加失败!'.$plansModel->getError(),2);
            }
            //成功
            self::redirect('index.php?p=Admin&c=Plans&a=index','添加成功!',2);
        }else{//添加列表展示
            //>>1.接受数据
            //>>2.处理数据
            //>>3.显示页面
            $this->display('add');
        }
    }
    //修改套餐
    public function edit(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] === "POST"){//修改保存
            //>>1.接受数据
            $data = $_POST;
            //>>2.处理数据
            $plansModel = new PlansModel();
            $rs = $plansModel->EditSave($data);
            //>>3.显示页面
            if ($rs === false){//失败
                self::redirect('index.php?p=Admin&c=Plans&a=edit&id='.$data['plan_id'],'修改失败!'.$plansModel->getError(),2);
            }
            //成功
            self::redirect('index.php?p=Admin&c=Plans&a=index','修改成功!',2);
        }else{//修改回显
            //>>1.接受数据
            $plan_id=$_GET['plan_id'];
            //>>2.处理数据
            $plansModel = new PlansModel();
            $row = $plansModel->Edit($plan_id);
            //>>3.显示页面
            //分配
            $this->assign($row);
            $this->display('edit');
        }
    }
    //删除
    public function delete(){
        //>>1.接受数据
        $plan_id = $_GET['plan_id'];
        //>>2.处理数据
        $plansModel = new PlansModel();
        $plansModel->Delete($plan_id);
        //>>3.显示页面
        self::redirect('index.php?p=Admin&c=Plans&a=index','删除成功!',2);
    }
}