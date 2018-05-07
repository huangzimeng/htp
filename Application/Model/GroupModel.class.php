<?php

/**
 * 部门 控制器
 */
class GroupModel extends Model
{

    //添加
    public function add($data){
        //判断是否填写名称
        if (empty($data['name'])){
            $this->error = '部门名称必须填写!';
            return false;
        }
        //部门名称不能重复添加
        $sql_c = "select `name` from `group` where `name`='{$data['name']}'";
        $c=$this->db->fetchRow($sql_c);
        if (!empty($c)){
            $this->error = "部门名称不能重复添加";
            return false;
        }
        //保存
        $sql = "INSERT INTO `group` SET `name`='{$data['name']}'";
        $this->db->execute($sql);
    }

    //回显部门
    public function getOne($id){
        return $this->db->fetchRow("SELECT * FROM `group` WHERE `group_id`='{$id}'");
    }

    //修改保存
    public function editSave($data){
        if (empty($data['name'])){
            $this->error = '部门名称必须填写!';
            return false;
        }
        //保存修改信息
        $this->db->execute("UPDATE `group` SET `name`='{$data['name']}' WHERE `group_id`='{$data['id']}'");
    }

    //删除部门
    public function delete($id){
        //判断部门中是否还有员工
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `members` WHERE `group_id`='{$id}'");
        if ($count > 0){
            $this->error = '该部门中还有员工,不能被删除!';
            return false;
        }
        //没有员工 可删除
        $this->db->execute("DELETE FROM `group` WHERE `group_id`='{$id}'");
    }

    //搜索+分页
    public function getpage($search=[],$page){
        $where = '';
        //判断是否搜索字段
        if (!empty($search)){
            $where = " WHERE ".implode(" AND ",$search);
        }
        //强转为数字
        $page = intval($page);
        //每页显示的条数
        $pagesize = 4;
        //总记录数
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `group`".$where);
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
        $list = $this->db->fetchAll("SELECT * FROM `group`".$where.$limit);
        //返回数组
        return ['list'=>$list,'count'=>$count,'pagesize'=>$pagesize,'totalpage'=>$totalpage,'page'=>$page];

    }


}