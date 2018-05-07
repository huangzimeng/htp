<?php

/**
 * 基础控制器
 */
abstract class Controller
{
    //关联数组,保存需要分配 到视图页面的数据
    private $datas = [];
    /**
     * 分配数据
     * 将 数据保存到 $datas 属性,必须 给出键名和值
     * @param $name 键名 字符串
     * @param $value 值
     * 第二种用法: 只传一个参数name,name必须为关联数组
     */
    public function assign($name,$value=''){
        if(is_array($name)){//当第一个参数为数组的时候,说明传入的是一个关联数组
            $this->datas = array_merge($this->datas,$name);
        }else{//name为字符串
            $this->datas[$name] = $value;
        }
    }
    /**
     * 显示页面
     * @param $template 模板的名称 可以不传,使用方法名作为模板的名称
     */
    public function display($template=''){
        if(empty($template)){//没有传入模板文件的名称
            $template = ACTION;
        }
        //将关联数组导入到当前符号表,键名作为变量,键值变量的值
        extract($this->datas);
        require CURRENT_VIEW_PATH."{$template}.html";
        exit;
    }
    /**
     * 跳转方法
     * @param $url 跳转的连接
     * @param string $msg 提示信息
     * @param int $times 间隔时间
     */
    protected static function redirect($url,$msg='',$times=0){
        $html =<<<HTML
    <style>
        body{
            background-color: #ecece7;
        }
        div{
        font-family: 楷体;
        text-align: center;
        font-size: 40px;
        color: red;
         padding:30px;
        }
    </style>
    <div>$msg</div>
HTML;
        echo $html;
        if($times>0){//提示信息
            header("Refresh: {$times}; {$url}");
        }else{//直接跳转
            header("Location: {$url}");
        }
        exit;
    }
}