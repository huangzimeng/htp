<?php

//日志模型
class HistoriesModel extends Model
{
    //获取所有数据
    public function getAll($page,$search=[])
    {
        $page = intval($page);
        //拼接查询条件
        $where = "";//没有条件
        if(!empty($search)){//判断条件数组是否为空,不为空才有条件
            $where .= " where ".implode(" and ",$search);
        }
        $pageSize = 10;
        $start = ($page-1)*$pageSize;
        $limit = " limit {$start},{$pageSize}";
        //获取总记录数
        $sql_count = "select count(*) from `histories`".$where;
        $count = $this->db->fetchColumn($sql_count);
        $totalPage = ceil($count/$pageSize);
        //判断
        $page = $page > $totalPage ? $totalPage : $page;
        $page = $page < 1 ? 1 : $page;
        //准备sql
        $order_by = "  ORDER BY history_id DESC";
        $sql = "select * from histories ".$where.$order_by.$limit;
        //执行sql
        $rows = $this->db->fetchAll($sql);
        foreach ($rows as &$v) {
            $sql_a = "select username from members where member_id='{$v['member_id']}'";//查询员工名称
            $a = $this->db->fetchColumn($sql_a);
            $sql_b = "select realname from users where user_id='{$v['user_id']}'";//查询用户姓名
            $b = $this->db->fetchColumn($sql_b);
            $v['username'] = $a;
            $v['realname'] = $b;
        }
        //返回
        return ['rows'=>$rows,'count'=>$count,'totalPage'=>$totalPage,'pageSize'=>$pageSize,'page'=>$page];
    }

}