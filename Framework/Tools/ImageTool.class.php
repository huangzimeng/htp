<?php
//制作缩略图
class ImageTool
{
    //保存错误信息
    private $error;
    public function getError(){
        return $this->error;
    }
    /** 制作缩略图
     ** @param $logo_path 原图路径
     * @param $thumb_width  目标图片的宽
     * @param $thumb_height 目标图片的高
     * @return  返回缩略图片的路径
     */
    public function thumb($logo_path,$thumb_width,$thumb_height){
        //1.创建原图
            //判断文件是否上传成功!
        if (!is_file($logo_path)){//原图不存在
            $this->error = "原图不存在";
            return false;
        }
            //获取原图的宽高
        $src_size = getimagesize($logo_path);
        list($src_width,$src_height) = $src_size;
            //可变方法名
            //得到原图片的mime类型
        $mime = $src_size['mime'];
//        var_dump($mime);die;
            //得到图片的格式
        $suffix = explode('/',$mime)[1];
            //拼接方法名称
        $create_func= "imagecreatefrom".$suffix;
            //可变方法名,
        $src_image = $create_func($logo_path);
        //2.创建新图
        $thumb_image = imagecreatetruecolor($thumb_width,$thumb_height);
        //3.将原图拷贝到新图
            //图片补白
        $white = imagecolorallocate($thumb_image,255,255,255);
        imagefill($thumb_image,0,0,$white);
            //等比例缩放
        $scale = max($src_width/$thumb_width,$src_height/$thumb_height);
            //计算原图缩放后的宽高
        $width = $src_width/$scale;
        $height = $src_height/$scale;
            //居中
        imagecopyresampled($thumb_image,$src_image,($thumb_width-$width)/2,($thumb_height-$height)/2,0,0,$width,$height,$src_width,$src_height);
        //4.输出新图
        $info = pathinfo($logo_path);
        $thumb_path = $info['dirname']."/".$info['filename']."_{$thumb_width}x{$thumb_height}.".$info['extension'];
        //拼接输出函数的函数名
        $out_func = "image".$suffix;
        $out_func($thumb_image,$thumb_path);
        //5.销毁图片
        imagedestroy($src_image);
        imagedestroy($thumb_image);
        //6.返回缩略图的路径
        return $thumb_path;
    }

}