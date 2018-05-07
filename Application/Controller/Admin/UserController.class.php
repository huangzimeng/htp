<?php

/**
 * 会员 控制器
 */
class UserController extends PlatformController
{
    //列表  搜索 + 分页
    public function index(){

        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        //接收参数
        $search = [];   //用来装 搜素条件
        if (!empty($_REQUEST['keyword'])){
            $search[] = "(`username` LIKE '%{$_REQUEST['keyword']}%' OR `realname` LIKE '%{$_REQUEST['keyword']}%' OR `telephone` LIKE '%{$_REQUEST['keyword']}%')";
        }
        //
        $page = $_REQUEST['page']??1;
        //处理数据
        $userModel = new UserModel();
        $userListResult = $userModel->getpage($search,$page);
        //利用http_build_query 拼接 链接
        unset($_REQUEST['page']);
        $url_params = http_build_query($_REQUEST);
        //调用分页工具
        $pageHtml = PageTool::show($userListResult['count'], $userListResult['totalpage'], $userListResult['pagesize'], $userListResult['page'], $url_params);

        //显示页面
        //分配
        $this->assign('userList',$userListResult['list']);
        $this->assign('pageHtml',$pageHtml);
        $this->display();

    }

    //添加会员
    public function add(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] == "POST"){
            //接收参数
            $data = $_POST;
            $files = $_FILES['photo'];
            //处理数据
            $userModel = new UserModel();
            $result = $userModel->addSave($data,$files);
            //显示页面
            if ($result === false){
                $this->redirect("index.php?p=Admin&c=User&a=add",$userModel->getError(),2);
            }
            $this->redirect("index.php?p=Admin&c=User&a=index",'添加成功',2);

        }else{
            //接收参数
            //处理数据
            //显示页面
            $this->display();
        }
    }

    //查看会员信息
    public function edit(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        $userModel = new UserModel();
        if ($_SERVER['REQUEST_METHOD'] == "POST"){
            //接收参数
            $data = $_POST;
            //处理数据
            $result = $userModel->edit($data);
            //显示页面
            if ($result === false){
                $this->redirect("index.php?p=Admin&c=User&a=edit&id={$data['id']}",$userModel->getError(),2);
            }
            $this->redirect("index.php?p=Admin&c=User&a=index",'修改成功',2);
        }else{
            $id = $_GET['id'];
            //处理数据

            $userinfo = $userModel->getOne($id);
            //显示页面
                //分配
            $this->assign($userinfo);
            $this->display();
        }
    }

    //删除会员
    public function delete(){
        //接收参数
        $id = $_GET['id'];
        //处理数据
        $userModel = new UserModel();
        $result = $userModel->delete($id);
        //显示页面
        if ($result === false){
            $this->redirect("index.php?p=Admin&c=User&a=index",$userModel->getError(),2);
        }
        $this->redirect("index.php?p=Admin&c=User&a=index",'删除成功!',2);
    }

    //会员充值
    public function recharge(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] === "POST"){//充值保存
            //>>1.接受数据
            $data = $_POST;
            //>>2.处理数据
            $userModel = new UserModel();
            $rs = $userModel->Recharge($data);
            //>>3.显示页面
            if ($rs === false){//失败
                self::redirect('index.php?p=Admin&c=User&a=recharge&id='.$data['user_id'],'充值失败!'.$userModel->getError(),2);
            }
            //成功
            self::redirect('index.php?p=Admin&c=User&a=index','充值成功!',2);
        }else{//充值表单展示
            //>>1.接受数据
            $user_id = $_GET['id'];
            //>>2.处理数据
            //>>3.显示页面
            $this->assign('user_id',$user_id);
            $this->display('recharge');
        }
    }

    //会员消费
    public function consumption(){
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] === "POST"){//消费保存
            //>>1.接受数据
            $data = $_POST;
            //>>2.处理数据
            //获取用户信息
            $userModel = new UserModel();
            $rs = $userModel->Consumption($data);
            if ($rs === false){
                self::redirect('index.php?p=Admin&c=User&a=consumption&id='.$data['user_id'],'消费失败!'.$userModel->getError(),2);
            }
            //>>3.显示页面
            self::redirect('index.php?p=Admin&c=User&a=index','已消费!',2);
        }else{//消费表单展示
            //>>1.接受数据
            $user_id = $_GET['id'];
            //>>2.处理数据
            //获取所有套餐信息
            $plansModel = new PlansModel();
            $plan = $plansModel->getPlans();
            //获取所有员工信息
            $memberModel = new MemberModel();
            $barber = $memberModel->getBarber();
            //>>3.显示页面
            $this->assign('plan',$plan);
            $this->assign('barber',$barber);
            $this->assign('user_id',$user_id);
            $this->display('consumption');
        }
    }
}