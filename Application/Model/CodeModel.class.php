<?php

/**
 * 代金券 模板
 */
class CodeModel extends Model
{
    //增加代金券
    public function add($data){
        //判断数据信息
        if (empty($data['number']) || empty($data['money'])){
            $this->error = '金额及数量必须填写!';
            return false;
        }
        //判断是否为数字
        if (!is_numeric($data['number']) || !is_numeric($data['money'])){
            $this->error = '请填写有效数字!';
            return false;
        }
        //
        if ($data['number']<1 || $data['money']<1){
            $this->error = '请填写有效数字!';
            return false;
        }
        for ($i=0 ; $i<$data['number'];$i++){
        $code = uniqid();
        $this->db->execute("INSERT INTO `codes` SET `code`='{$code}',`money`='{$data['money']}'");
        }
    }

    //首页显示 + 分页 搜索
    public function getpage($search=[],$page){
        $where = '';

        if (!empty($search)){
            $where = " WHERE ".implode(" AND ",$search);
        }
        //强转为数字
        $page = intval($page);
        //每页显示的条数
        $pagesize = 8;
        //总条数
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `codes`".$where);
        //总页数
        $totalpage = ceil($count/$pagesize);
        //限制输入的页数
        $page = $page > $totalpage ? $totalpage : $page;
        $page = $page < 1 ? 1 : $page;
        //开始位置
        $start = ($page-1)*$pagesize;
        //拼接 limit 语句
        $limit = " LIMIT {$start},{$pagesize}";
        //查询数据
        $list = $this->db->fetchAll("SELECT * FROM `codes`".$where." ORDER BY `code_id` DESC ".$limit);
            //根据会员 id 查询会员名字保存在数组中
        foreach($list as &$v){
            $v['name'] = $this->db->fetchColumn("SELECT `username` FROM `users` WHERE `user_id`='{$v['user_id']}'");
        }

        //返回数组
        return ['list'=>$list,'count'=>$count,'pagesize'=>$pagesize,'totalpage'=>$totalpage,'page'=>$page];

    }

    //删除代金券
    public function delete($id){
        $money = $this->db->fetchColumn("SELECT `money` FROM `codes` WHERE `code_id`='{$id}'");
        if ($money>0){
            $this->error = '该代金券未使用完!';
            return false;
        }
        return $this->db->execute("DELETE FROM `codes` WHERE `code_id`='{$id}'");

    }

    //查看代金券信息
    public function getOne($id){
        $list = $this->db->fetchRow("SELECT * FROM `codes` WHERE `code_id`='{$id}'");
        //根据会员 id 查询会员名字保存在数组中
        $list['name'] = $this->db->fetchColumn("SELECT `username` FROM `users` WHERE `user_id`='{$list['user_id']}'");
        return $list;
    }

    //修改代金券
    public function edit($data){
        //判断金额 必须输入
        if (empty($data['money'])){
            $this->error = '金额不能为空!';
            return false;
        }
        //判断金额 必须为数字 并且大于0
        if (!is_numeric($data['money']) || $data['money'] <1){
            $this->error = '填写有效金额!';
            return  false;
        }
        //修改代金券
        if (isset($data['user_id'])){
            $this->db->execute("UPDATE `codes` SET `money`='{$data['money']}',`user_id`='{$data['user_id']}' WHERE `code_id`='{$data['id']}'");
        }else{
            $this->db->execute("UPDATE `codes` SET `money`='{$data['money']}' WHERE `code_id`='{$data['id']}'");
        }

    }


}