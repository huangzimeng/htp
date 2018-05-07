<?php
// 前台 首页 控制器
class IndexController extends PlatformController
{
    //前台首页展示
    public function index(){
        //>>1.接受数据
        $page = $_GET['page']??1;
        $search = [];  //搜索条件
        if (!empty($_REQUEST['keyword'])){
            $search[] = " (`shop_name` LIKE '%{$_REQUEST['keyword']}%' OR `money` LIKE '%{$_REQUEST['keyword']}%' OR `intro` LIKE '%{$_REQUEST['keyword']}%')";
        }
        @session_start();
        $user_id = $_SESSION['USER_INFO']['user_id']??'';
        $username = $_SESSION['USER_INFO']['username']??'';
        $photo = $_SESSION['USER_INFO']['photo']??'';
        //获取排行榜信息
        $rankModel = new RankModel();
        $list1 = $rankModel->index1();
        $list2 = $rankModel->index2();
        $list3 = $rankModel->index3();
        //获取美发师员工信息
        $memberModel = new MemberModel();
        $barber = $memberModel->getBarber();
        //获取套餐信息
        $plansModel = new PlansModel();
        $plans = $plansModel->getPlans();
        //获取商城信息
        $shopModel = new ShopModel();
        $shopList = $shopModel->getpage($search,$page);
        //获取积分信息
        $integralModel = new IntegralModel();
        $integral = $integralModel->getOne($user_id);
        //获取最新的 有效的活动信息
        $activityModel = new ActivityModel();
        $activityList = $activityModel->getAll();

            //积分商城 显示分页
            //利用http_build_query 拼接 链接
        unset($_REQUEST['page']);
        $url_params = http_build_query($_REQUEST);
        //调用分页工具
        $pageHtml = PageTool::show($shopList['count'], $shopList['totalpage'], $shopList['pagesize'], $shopList['page'], $url_params);

        //>>3.显示页面
        $this->assign('shopList',$shopList['list']);
        $this->assign('pageHtml',$pageHtml);
            //分配积分
        $this->assign($integral);
            //
        $this->assign('plans',$plans);
        $this->assign('barber',$barber);
        $this->assign('photo',$photo);
        $this->assign('username',$username);
            //分配 排行榜信息
        $this->assign('list1',$list1['result']);
        $this->assign('list2',$list2['result']);
        $this->assign('list3',$list3['result']);
            //分配 活动信息
        $this->assign('activityList',$activityList);

        //显示列表
        $this->display();
    }


    //修改个人资料
    public function modify(){
        if ($_SERVER['REQUEST_METHOD'] == "POST"){//修改个人资料保存
            //>>1.接受数据
            $data  = $_POST;
            $file = $_FILES['photo'];
            //>>2.1上传文件
            $uploadTool = new UploadTool();
            $photo_path = $uploadTool->upload($file, 'Member/');
            //上传文件成功,制作缩略图
            //>>2.2制作缩略图
            $imageTool = new ImageTool();
            $photo_thumb_path = $imageTool->thumb($photo_path, 50, 50);
            //制作缩略图失败
            if ($photo_thumb_path !== false) {
                //成功!保存缩略图路径到$data
                $data['photo'] = $photo_thumb_path;
            }
            //>>2.处理数据
            $userModel = new UserModel();
            $rs = $userModel->ModifySave($data);
            //>>3.显示页面
            if ($rs === false){//失败
                self::redirect('index.php?p=Home&c=Index&a=modify','修改失败!'.$userModel->getError(),2);
            }
            //成功
            self::redirect('index.php?p=Home&c=Login&a=out','修改成功!',2);
        }else{//回显个人资料
            //>>1.接受数据
            @session_start();
            $user_id = $_SESSION['USER_INFO']['user_id'];
            //>>2.处理数据
            $userModel = new UserModel();
            $rs = $userModel->Modify($user_id);
            //>>3.显示页面
            //分配
            $this->assign($rs);
            $this->display('modify');
        }


    }



}