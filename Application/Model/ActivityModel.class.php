<?php

/**
 * 活动 模型
 */
class ActivityModel extends Model
{
    //添加活动
    public function insert($data){
        //判断信息完整
        if (empty($data['title'])){
            $this->error = '活动标题必须填写!';
            return false;
        }
        if (empty($data['content'])){
            $this->error = '活动内容不能为空!';
            return false;
        }
        if (empty($data['intro'])){
            $this->error = '活动简介不能为空!';
            return false;
        }
        //判断时间
        $time = time();
        $start = strtotime($data['start']);
        $end = strtotime($data['end']);
        if ($start > $end){
            $this->error = '结束时间不能在开始时间之前!';
            return false;
        }
        if ($end < $time){
            $this->error = '结束时间不能再现在时间之前!';
            return false;
        }
        if ($time > $start){
            $this->error = '开始时间不能是现在时间之前!';
            return false;
        }
        //保存活动信息
        return $this->db->execute("INSERT INTO `activity` SET `title`='{$data['title']}',`content`='{$data['content']}',`start`='{$start}',`end`='{$end}',`time`='{$time}',`intro`='{$data['intro']}'");
    }

    //回显
    public function getOne($id){
        return $this->db->fetchRow("SELECT * FROM `activity` WHERE `activity_id`='{$id}'");
    }

    //修改活动
    public function update($data){
        //判断信息完整
        if (empty($data['title'])){
            $this->error = '活动标题必须填写!';
            return false;
        }
        if (empty($data['content'])){
            $this->error = '活动内容不能为空!';
            return false;
        }
        if (empty($data['intro'])){
            $this->error = '活动简介不能为空!';
            return false;
        }

        //判断时间
        $time = time();
        $start = strtotime($data['start']);
        $end = strtotime($data['end']);

        if ($start > $end){
            $this->error = '结束时间不能在开始时间之前!';
            return false;
        }

        //保存修改信息
        return $this->db->execute("UPDATE `activity` SET `title`='{$data['title']}',`content`='{$data['content']}',`time`='{$time}',`start`='{$start}',`end`='{$end}',`intro`='{$data['intro']}' WHERE `activity_id`='{$data['id']}'");
    }

    //删除活动
    public function delete($id){
        //判断是否是正在进行的活动
        $time = time();
        $start = $this->db->fetchColumn("SELECT `start` FROM `activity` WHERE `activity_id`='{$id}'");
        if ($time > $start){
            $this->error = '活动正在进行中,不能删除!';
            return false;
        }
        //删除未开始的活动
        return $this->db->execute("DELETE FROM `activity` WHERE `activity_id`='{$id}'");
    }

    //搜索+分页
    public function getpage($search=[],$page){
        //获取现在的时间
        $time = time();
        //初始where语句
        $where = " WHERE `end`>'{$time}'";
        //判断是否搜索字段
        if (!empty($search)){
            $where =$where. " AND ".implode(" AND ",$search);
        }
        //强转为数字
        $page = intval($page);
        //每页显示的条数
        $pagesize = 8;
        //总记录数
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `activity`".$where);
        //总页数
        $totalpage = ceil($count/$pagesize);
        //限制输入的页数
        $page = $page > $totalpage ? $totalpage : $page;
        $page = $page < 1 ? 1 : $page;
        //开始位置
        $start = ($page-1)*$pagesize;
        //拼接 limit 语句
        $limit = " LIMIT {$start},{$pagesize}";
        //查询搜索
        $list = $this->db->fetchAll("SELECT * FROM `activity`".$where." order by `activity_id` desc ".$limit);
        //返回数组
        return ['list'=>$list,'count'=>$count,'pagesize'=>$pagesize,'totalpage'=>$totalpage,'page'=>$page];

    }

    //前台查询有效的活动
    public function getAll(){
        //获取现在时间
        $time = time();
        return $this->db->fetchAll("SELECT * FROM activity WHERE `end`>'{$time}' ORDER BY `activity_id` DESC");
    }

}