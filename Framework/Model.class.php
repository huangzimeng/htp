<?php

/**
 * 基础模型类
 */
abstract class Model
{
    //保存创建好的DB对象
    protected $db;
    //保存错误信息
    protected $error;
    /**
     * 初始化
     */
    public function __construct()
    {
        $this->db = DB::getInstance($GLOBALS['config']['db']);
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError(){
        return $this->error;
    }
}