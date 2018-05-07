<?php

/**
 * 积分 模型
 */
class IntegralModel extends Model
{
    //查询积分
    public function getOne($id){
        return $this->db->fetchRow("SELECT * FROM `integral` WHERE `integral_id`='{$id}'");
    }

    //

}