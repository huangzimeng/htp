<?php

//预约模型
class OrderModel extends Model
{
    //前台添加预约
    public function Add($data){
        //1.真实姓名不能为空
        if (empty($data['realname'])){
            $this->error = "真实姓名不能为空!";
            return false;
        }
        //2.电话不能为空
        if (empty($data['phone'])){
            $this->error = "电话不能为空!";
        }
        //3.电话必须为数字
        if (!is_numeric($data['phone'])){
            $this->error = "电话必须为数字";
            return false;
        }
        //4.查询姓名是否填写正确
        $sql_re = "select realname from users where realname='{$data['realname']}'";
        $count = $this->db->fetchRow($sql_re);
        if (!isset($count['realname'])){
            $this->error = "姓名输入错误!";
            return false;
        }
        //5.不能重复预约
        $sql_r = "select realname from `order` where realname='{$data['realname']}'";
        $r = $this->db->fetchRow($sql_r);
        if (!empty($r)){
            $this->error = "不能重复预约";
            return false;
        }
        $date = strtotime($data['date']);
        $time = time();
        //6.时间不能小于当前时间
        if ($time>$date){
            $this->error = "时间不能小于当前时间";
            return false;
        }
        //准备sql
        $sql = "insert into `order` set
`realname`='{$data['realname']}',
`phone`='{$data['phone']}',
`plans`='{$data['plans']}',
`barber`='{$data['barber']}',
`date`='{$date}',
`content`='{$data['content']}'
";
        //执行sql
        $this->db->execute($sql);
    }
    //后台获取所有数据
    public function getAll($page,$search=[]){
        $page = intval($page);
        //拼接查询条件
        $where = "";//没有条件
        if(!empty($search)){//判断条件数组是否为空,不为空才有条件
            $where .= " where ".implode(" and ",$search);
        }
        $pageSize = 5;
        $start = ($page-1)*$pageSize;
        $limit = " limit {$start},{$pageSize}";
        //获取总记录数
        $sql_count = "select count(*) from `order`".$where;
        $count = $this->db->fetchColumn($sql_count);
        $totalPage = ceil($count/$pageSize);
        //判断
        $page = $page > $totalPage ? $totalPage : $page;
        $page = $page < 1 ? 1 : $page;
        //
        //准备Sql
        $sql = "select * from `order`".$where.$limit;
        $rows =  $this->db->fetchAll($sql);
        foreach ($rows as &$v){
            $sql_p = "select `name` from plans where plan_id='{$v['plans']}' and status=1";
            $sql_m = "select username from members where member_id='{$v['barber']}'";
            $b = $this->db->fetchColumn($sql_p);
            $a = $this->db->fetchColumn($sql_m);
            $v['username'] = $a;
            $v['name'] = $b;
        }
        //返回
        return ['rows'=>$rows,'count'=>$count,'totalPage'=>$totalPage,'pageSize'=>$pageSize,'page'=>$page];

    }
    //处理订单
    public function Deal($order_id){
        //准备sql
        $sql = "update `order` set status=1 where order_id='{$order_id}'";
        //执行
        $this->db->execute($sql);
    }
    //回复订单
    public function Reply($data){
        //1.回复内容不能为空
        if (empty($data['reply'])){
            $this->error = "回复不能为空!";
            return false;
        }
        //准备sql
        $sql = "update  `order` set reply='{$data['reply']}' where order_id='{$data['order_id']}'";
        //执行
        $this->db->execute($sql);
    }
    //删除订单
    public function Delete($order_id){
        //查询订单信息
        $sql_c = "select * from `order` where order_id='{$order_id}'";
        $c = $this->db->fetchRow($sql_c);
        if ($c['date'] > time()){
            $this->error = "没有过期不能删除!";
            return false;
        }
        //准备sql
        $sql = "delete from `order` where order_id='{$order_id}'";
        //执行
        $this->db->execute($sql);
    }

}