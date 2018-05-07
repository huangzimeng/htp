<?php

//套餐模型
class PlansModel extends Model
{
    //列表首页
    public function getAll(){
        //准备sql
        $sql = "select * from plans";
        //执行sql
        return $this->db->fetchAll($sql);
    }
    //添加数据
    public function Insert($data){
        //1.套餐名称不能为空
        if (empty($data['name'])){
            $this->error = "套餐名称不能为空!";
            return false;
        }
        //2.描述不能为空
        if (empty($data['des'])){
            $this->error = "描述不能为空!";
            return false;
        }
        //3.金钱不能为空
        if (empty($data['money'])){
            $this->error = "金额不能为空!";
            return false;
        }
        //4.请选择套餐状态
        if (!isset($data['status'])){
            $this->error = "请选择套餐状态!";
            return false;
        }
        //准备sql
        $sql = "insert into plans set 
`name`='{$data['name']}',
`des`='{$data['des']}',
`money`='{$data['money']}',
`status`='{$data['status']}'
";
        //执行sql
        $this->db->execute($sql);
    }
    //修改回显
    public function Edit($plan_id){
        //准备sql
        $sql = "select * from plans where plan_id='{$plan_id}'";
        //执行sql
        return $this->db->fetchRow($sql);
    }
    //修改保存
    public function EditSave($data){
        //1.套餐名称不能为空
        if (empty($data['name'])){
            $this->error = "套餐名称不能为空!";
            return false;
        }
        //2.描述不能为空
        if (empty($data['des'])){
            $this->error = "描述不能为空!";
            return false;
        }
        //3.金钱不能为空
        if (empty($data['money'])){
            $this->error = "金额不能为空!";
            return false;
        }
        //4.请选择套餐状态
        if (!isset($data['status'])){
            $this->error = "请选择套餐状态!";
            return false;
        }
        //准备sql
        $sql = "update plans set 
`name`='{$data['name']}',
`des`='{$data['des']}',
`money`='{$data['money']}',
`status`='{$data['status']}' where plan_id='{$data['plan_id']}'
";
        //执行sql
        $this->db->execute($sql);
    }
    //删除套餐
    public function Delete($plan_id){
        //准备Sql
        $sql = "delete from plans where plan_id='{$plan_id}'";
        //执行
        $this->db->execute($sql);
    }
    //获取所有套餐信息
    public function getPlans(){
        //准备sql
        $sql = "select * from plans where status=1";
        //执行sql
        return $this->db->fetchAll($sql);
    }

    //分页+搜索
    public function getpage($search=[],$page){
        $where = '';
        //判断是否搜索字段
        if (!empty($search)){
            $where = " WHERE ".implode(" AND ",$search);
        }
        //强转为数字
        $page = intval($page);
        //每页显示的条数
        $pagesize = 5;
        //总记录数
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `plans`".$where);
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
        $list = $this->db->fetchAll("SELECT * FROM `plans`".$where.$limit);
        //返回数组
        return ['list'=>$list,'count'=>$count,'pagesize'=>$pagesize,'totalpage'=>$totalpage,'page'=>$page];

    }
}