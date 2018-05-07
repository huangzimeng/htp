<?php

//注册控制器
class RegisterController extends PlatformController
{
    //注册表单展示
    public function index(){
        //>>1.接受数据
        //>>2.处理数据
        //>>3.显示页面
        $this->display('index');
    }
    //注册保存
    public function add(){
        //>>1.接受数据
        $data = $_POST;
        $file = $_FILES['photo'];
        //>>2.1上传文件
        $uploadTool = new UploadTool();
        $photo_path = $uploadTool->upload($file, 'ZhuCe/');
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
        $rs = $userModel->register($data);
        //>>3.显示页面
        if ($rs === false){
            self::redirect('index.php?p=Home&c=Register&a=index','注册失败!'.$userModel->getError(),2);
        }
        //成功
        self::redirect('index.php?p=Home&c=Register&a=index','注册成功!请登录',2);
    }

}