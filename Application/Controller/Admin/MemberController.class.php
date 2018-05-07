<?php

//员工控制器
class MemberController extends PlatformController
{
    //列表
    public function index()
    {
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        //接收参数
        $search = [];   //用来装 搜素条件
        if (!empty($_REQUEST['keyword'])){
            $search[] = "(`username` LIKE '%{$_REQUEST['keyword']}%' OR `realname` LIKE '%{$_REQUEST['keyword']}%' OR `telephone` LIKE '%{$_REQUEST['keyword']}%')";
        }
        //
        $page = $_GET['page']??1;
        //处理数据
        $memberModel = new MemberModel();
        $memberListResult = $memberModel->getpage($search,$page);
        //利用http_build_query 拼接 链接
        unset($_REQUEST['page']);
        $url_params = http_build_query($_REQUEST);
        //调用分页工具
        $pageHtml = PageTool::show($memberListResult['count'], $memberListResult['totalpage'], $memberListResult['pagesize'], $memberListResult['page'], $url_params);

        //显示页面
            //分配
        $this->assign('memberList',$memberListResult['list']);
        $this->assign('pageHtml',$pageHtml);
        $this->display();
    }

    //添加数据
    public function add()
    {
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] == "POST") {//添加的保存
            //>>1.接受数据
            $data = $_POST;
            $file = $_FILES['photo'];
            //>>2.处理数据
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
            $memberModel = new MemberModel();
            $rs = $memberModel->Insert($data);
            //>>3.显示页面
            if ($rs === false) {//失败
                self::redirect('index.php?p=Admin&c=Member&a=add', '添加失败!' . $memberModel->getError(), 2);
            }
            //成功
            self::redirect('index.php?p=Admin&c=Member&a=index', '添加成功!', 2);
        } else {//添加页面的展示
            //>>1.接受数据
            //>>2.处理数据
            $memberModel = new MemberModel();
            $rows = $memberModel->getGroup();
            //>>3.显示页面
            //分配
            $this->assign('rows', $rows);
            $this->display('add');
        }

    }

    //修改数据
    public function edit()
    {
        @session_start();
        $this->assign('info',$_SESSION['INFO']);

        if ($_SERVER['REQUEST_METHOD'] == "POST") {//修改的保存
            //>>1.接受数据
            $data = $_POST;
            $file = $_FILES['photo'];
            //>>2.处理数据
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
            $memberModel = new MemberModel();
            $rs = $memberModel->EditSave($data);
            //>>3.显示页面
            if ($rs === false) {
                self::redirect('index.php?p=Admin&c=Member&a=edit&member_id=' . $data['member_id'], $memberModel->getError(), 2);
            }
            self::redirect('index.php?p=Admin&c=Member&a=index', '修改成功!', 2);
        } else {//修改的回显
            //>>1.接受数据
            $member_id = $_GET['member_id'];
            //>>2.处理数据
            $memberModel = new MemberModel();
            $row = $memberModel->Edit($member_id);
            //获取所有分组信息
            $memberModel = new MemberModel();
            $rows = $memberModel->getGroup();
            //>>3.显示页面
            //分配
            $this->assign('rows', $rows);
            $this->assign($row);
            $this->display('edit');
        }
    }

    //删除数据
    public function delete()
    {
        //>>1.接受数据
        $member_id = $_GET['member_id'];
        //>>2.处理数据
        $memberModel = new MemberModel();
        $rs = $memberModel->Delete($member_id);
        //>>3.显示页面
        if ($rs === false){
            self::redirect('index.php?p=Admin&c=Member&a=index','删除失败!'.$memberModel->getError(),2);
        }
        self::redirect('index.php?p=Admin&c=Member&a=index', '删除成功!', 2);
    }
}