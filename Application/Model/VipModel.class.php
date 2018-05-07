<?php

//会员等级模型
class VipModel extends Model
{
    //获取所有数据
    public function getAll(){
        //准备sql
        $sql = "select * from vip ORDER BY discount DESC";
        //执行
        return $this->db->fetchAll($sql);
    }
    //添加数据
    public function Add($data){
        //1.vip等级不能为空
        if (empty($data['rating'])){
            $this->error = "vip等级不能为空!";
            return false;
        }
        //2.折扣不能为空
        if (empty($data['discount'])){
            $this->error = "折扣不能为空!";
            return false;
        }
        //3.达到等级的条件不能为空
        if (empty($data['condition'])){
            $this->error = "达到等级的条件不能为空";
            return false;
        }
        //4.vip等级必须为数字
        if (!is_numeric($data['rating'])){
            $this->error = "vip等级必须为数字!";
            return false;
        }
        //5.折扣必须为数字
        if (!is_numeric($data['discount'])){
            $this->error = "vip等级必须为数字!";
            return false;
        }
        //6.达到等级的条件不能为空
        if (!is_numeric($data['condition'])){
            $this->error = "vip等级必须为数字!";
            return false;
        }
        //准备sql
        $sql = "insert into `vip` set 
`rating`='{$data['rating']}',
`discount`='{$data['discount']}',
`condition`='{$data['condition']}'";
        //执行
        $this->db->execute($sql);
    }
    ///修改回显
    public function Edit($vip_id){
        //准备Sql
        $sql = "select * from vip where vip_id='{$vip_id}'";
        //执行Sql
        return $this->db->fetchRow($sql);
    }
    //修改保存
    public function EdirSave($data){
        //1.折扣不能为空
        if (empty($data['discount'])){
            $this->error = "折扣不能为空!";
            return false;
        }
        //2.达到等级的条件不能为空
        if (empty($data['condition'])){
            $this->error = "达到等级的条件不能为空";
            return false;
        }
        //3.折扣必须为数字
        if (!is_numeric($data['discount'])){
            $this->error = "vip等级必须为数字!";
            return false;
        }
        //4.达到等级的条件不能为空
        if (!is_numeric($data['condition'])){
            $this->error = "vip等级必须为数字!";
            return false;
        }
        //准备sql
        $sql = "update `vip` set `discount`='{$data['discount']}',`condition`='{$data['condition']}' where vip_id='{$data['vip_id']}'";
        //执行
        $this->db->execute($sql);
    }
    //删除
    public function Delete($vip_id){
        //准备sql
        $sql = "delete from `vip` where vip_id='{$vip_id}'";
        //执行
        $this->db->execute($sql);
    }
}