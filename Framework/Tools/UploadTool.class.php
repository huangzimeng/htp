<?php
//文件上传
class UploadTool
{
    //保存错误信息
    private $error;
    public function getError(){
        return $this->error;
    }
    //上传文件
    public function upload($file_info,$type){
        //1.判断文件上传是否出错
        if ($file_info['error'] != 0){
            $this->error = "文件上传失败!";
            return false;
        }
        //2.判断文件上传的类型是否正确
        $allow_type = ['image/png','image/jpeg','image/gif'];
        if (!in_array($file_info['type'],$allow_type)){
            $this->error = "图片类型错误!";
            return false;
        }
        //3.判断上传文件的大小
        if ($file_info['size']>1024*1024*2){
            $this->error = "图片不能大于2M";
            return false;
        }
        //4.判断上传文件的真正类型
        $size = @getimagesize($file_info['tmp_name']);
            if ($size === false){
                $this->error = "不是真正的图片类型!";
                return false;
            }
        //5.判断是否是通过http post上传的
        if (!is_uploaded_file($file_info['tmp_name'])){
                $this->error = "不是通过HTTP POST上传的!";
        }
        //处理文件上传的目录
        $dirname = "./Uploads/{$type}".date("Ymd").'/';
        //判断目录是否存在
        if (!is_dir($dirname)){//不存在
            mkdir($dirname,0777,true);//创建目录
        }
        //文件名称
        $filename = uniqid("img_").strrchr($file_info['name'],".");
        //移动
        if (!move_uploaded_file($file_info['tmp_name'],$dirname.$filename)){
            $this->error = "移动文件失败!";
            return false;
        }else{
            //成功!
            return $dirname.$filename;
        }
    }
}