<?php

//充值活动模型
class RuleModel extends Model
{
    //获取所有数据
    public function getAll(){
        //准备sql
        $sql = "select * from rule";
        //执行sql
        return $this->db->fetchAll($sql);
    }
    //添加
    public function Add($data){
        //1.充值金额不能为空
        if (empty($data['remoney'])){
            $this->error = "充值金额不能为空!";
            return false;
        }
        //2.赠送金额不能为空
        if (empty($data['sendmoney'])){
            $this->error = "赠送金额不能为空!";
            return false;
        }
        //3.充值金额必须为数字
        if (!is_numeric($data['remoney'])){
            $this->error = "充值金额必须为数字!";
            return false;
        }
        //4.赠送金额必须为数字
        if (!is_numeric($data['sendmoney'])){
            $this->error = "赠送金额必须为数字!";
            return false;
        }
        //5.说明不能为空
        if (empty($data['desc'])){
            $this->error = "说明不能为空";
            return false;
        }
        //准备sql
        $sql = "insert into `rule` set 
`remoney`='{$data['remoney']}',
`desc`='{$data['desc']}',
`sendmoney`='{$data['sendmoney']}'";
        //执行
        $this->db->execute($sql);
    }
    //修改
    public function Edit($rule_id){
        //准备sql
        $sql = "select * from  `rule` where rule_id='{$rule_id}'";
        //执行
        return $this->db->fetchRow($sql);
    }
    //修改保存
    public function EditSave($data){
        //1.充值金额不能为空
        if (empty($data['remoney'])){
            $this->error = "充值金额不能为空!";
            return false;
        }
        //2.赠送金额不能为空
        if (empty($data['sendmoney'])){
            $this->error = "赠送金额不能为空!";
            return false;
        }
        //3.充值金额必须为数字
        if (!is_numeric($data['remoney'])){
            $this->error = "充值金额必须为数字!";
            return false;
        }
        //4.赠送金额必须为数字
        if (!is_numeric($data['sendmoney'])){
            $this->error = "赠送金额必须为数字!";
            return false;
        }
        //5.说明不能为空
        if (empty($data['desc'])){
            $this->error = "说明不能为空";
            return false;
        }
        //准备sql
        $sql = "update `rule` set `remoney`='{$data['remoney']}',`desc`='{$data['desc']}',`sendmoney`='{$data['sendmoney']}' where `rule_id`='{$data['rule_id']}'";
        //执行
        $this->db->execute($sql);
    }
    //删除
    public function Delete($rule_id){
        //准备sql
        $sql = "delete from `rule` where rule_id='{$rule_id}'";
        //执行
        $this->db->execute($sql);
    }

}