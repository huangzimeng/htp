<?php

//导出模型
class ExportModel extends Model
{
    //获取导出的数据
    public function gelAll(){
        //准备sql
        $sql = "select * from users";
        return $this->db->fetchAll($sql);
    }
}