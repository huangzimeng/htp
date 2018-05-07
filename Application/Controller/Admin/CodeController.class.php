<?php

/**
 * 代金券 控制器
 */
class CodeController extends PlatformController
{
    //列表
    public function index(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        $search = [];
        //接收参数
        if (!empty($_REQUEST['keyword'])){
            $search[] = " (`code` LIKE '%{$_REQUEST['keyword']}%' OR `money`='{$_REQUEST['keyword']}')";
        }
        //处理输入的
        $page = $_GET['page']??1;
        //处理数据
        $codeModel = new CodeModel();
        $codeListResult = $codeModel->getpage($search,$page);
        //利用http_build_query 拼接 链接
        unset($_REQUEST['page']);
        $url_params = http_build_query($_REQUEST);
        //调用分页工具
        $pageHtml = PageTool::show($codeListResult['count'], $codeListResult['totalpage'], $codeListResult['pagesize'], $codeListResult['page'], $url_params);

        //显示页面
            //分配
        $this->assign('pageHtml',$pageHtml);
        $this->assign('codeList',$codeListResult['list']);
        $this->display();
    }

    //新增代金券
    public function add(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] == "POST"){
            //接收参数
            $data = $_POST;
            //处理数据
            $codeModel = new CodeModel();
            $result = $codeModel->add($data);
            //显示页面
            if ($result === false){
                $this->redirect("index.php?p=Admin&c=Code&a=add",$codeModel->getError(),2);
            }
            $this->redirect("index.php?p=Admin&c=Code&a=index",'添加成功!',2);


        }else{
            //接收参数
            //处理数据
            //显示页面
            @session_start();
            $this->assign($_SESSION['INFO']);
            $this->display();
        }

    }

    //查看代金券
    public function edit(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        $codeModel = new CodeModel();
        if ($_SERVER['REQUEST_METHOD'] == "POST"){
            //接收参数
            $data = $_POST;
            //处理数据
            $result = $codeModel->edit($data);
            //显示页面
            if ($result === false){
                $this->redirect("index.php?p=Admin&c=Code&a=index",$codeModel->getError(),2);
            }
            $this->redirect("index.php?p=Admin&c=Code&a=index",'修改成功!',2);

        }else{
            //接收参数
            $id = $_GET['id'];
            //处理数据
            $code = $codeModel->getOne($id);
                //如果未绑定会员 查询会员表
                if ($code['user_id']<1){
                    $userModel = new UserModel();
                    $userList = $userModel->getAll();
                    $this->assign('userList',$userList);
                }
            //显示页面
                //分配
            $this->assign($code);
            $this->display();
        }

    }

    //删除代金券
    public function delete(){
        //接收参数
        $id = $_GET['id'];
        //处理数据
        $codeModel = new CodeModel();
        $result = $codeModel->delete($id);
        //显示页面
        if ($result === false){
            $this->redirect("index.php?p=Admin&c=Code&a=index",$codeModel->getError(),2);
        }
        $this->redirect("index.php?p=Admin&c=Code&a=index",'删除成功!',2);


    }


}