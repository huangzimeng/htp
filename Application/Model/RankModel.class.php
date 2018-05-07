<?php

/**
 * 前台 排行榜 模型
 */
class RankModel extends Model
{
    //服务之星
    public function index1()
    {
        //去重复查询 服务总次数前三的  次数
        $count = $this->db->fetchAll("SELECT DISTINCT  count(member_id) as count FROM histories GROUP BY member_id ORDER BY count DESC limit 3");
        //循环查询 前三次次数 中对应的 member_id
        $result = [];
        foreach ($count as $key=>$value) {
            $list = $this->db->fetchAll("SELECT member_id,count(member_id) as count FROM histories GROUP BY member_id HAVING count='{$value['count']}'");
            //查询member表单 查出 name
            //查询并列
            foreach ($list as $v) {
                $result[$key][] = $this->db->fetchColumn("SELECT `realname` FROM `members` WHERE `member_id`='{$v['member_id']}'");
            }
        }
        return ['result'=>$result,'data'=>$count];

    }

    //充值之星
    public function index2(){
        //去重复查询
        $result = [];
        $sum = $this->db->fetchAll("SELECT DISTINCT  sum(amount) as `sum` FROM histories WHERE `type`=0 GROUP BY user_id ORDER BY `sum` DESC limit 3");
        //循环查询 对应金额下的 user_id
        foreach ($sum as $key=>$value) {
            $list = $this->db->fetchAll("SELECT user_id,sum(amount) as `sum` FROM histories WHERE `type`=0 GROUP BY `user_id` HAVING `sum`='{$value['sum']}'");
            foreach ($list as $v){
                $result[$key][] = $this->db->fetchColumn("SELECT `realname` FROM `users` WHERE `user_id`='{$v['user_id']}'");
            }
        }
        return ['result'=>$result,'data'=>$sum];
    }

    //消费之星
    public function index3(){
        //去重复查询
        $result = [];
        $sum = $this->db->fetchAll("SELECT DISTINCT  sum(amount) as `sum` FROM histories WHERE `type`=1 GROUP BY user_id ORDER BY `sum` DESC limit 3");
        //循环查询 对应金额下的 user_id
        foreach ($sum as $key=>$value) {
            $list = $this->db->fetchAll("SELECT user_id,sum(amount) as `sum` FROM histories WHERE `type`=1 GROUP BY `user_id` HAVING `sum`='{$value['sum']}'");
            //并列情况
            foreach ($list as $v){
                $result[$key][] = $this->db->fetchColumn("SELECT `realname` FROM `users` WHERE `user_id`='{$v['user_id']}'");
            }
        }
        return ['result'=>$result,'data'=>$sum];
    }



}