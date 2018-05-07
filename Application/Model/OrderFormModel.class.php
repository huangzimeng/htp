<?php

/**
 * 订单处理 模型
 */
class OrderFormModel extends Model
{
    //后台显示 订单列表 搜索+分页
    public function getpage($search=[],$page){
        $where = " WHERE `status`!='-1'";
        //判断是否搜索字段
        if (!empty($search)){
            $where = $where." AND ".implode(" AND ",$search);
        }
        //强转为数字
        $page = intval($page);
        //每页显示的条数
        $pagesize = 8;
        //总记录数
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `order_form`".$where." ORDER BY `order_form_id` DESC ");
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
        $list = $this->db->fetchAll("SELECT * FROM `order_form`".$where." ORDER BY `order_form_id` DESC ".$limit);
        foreach($list as &$v){
            $v['username'] = $this->db->fetchColumn("SELECT `username` FROM `users` WHERE `user_id`='{$v['user_id']}'");
        }
        foreach($list as &$v){
            $v['membername'] = $this->db->fetchColumn("SELECT `username` FROM `members` WHERE `member_id`='{$v['member_id']}'");
        }

        //返回数组
        return ['list'=>$list,'count'=>$count,'pagesize'=>$pagesize,'totalpage'=>$totalpage,'page'=>$page];
    }

    //新增订单
    public function insert($data){
        //根据id 查找相应的 积分
        $intinfo = $this->db->fetchRow("SELECT * FROM `integral` WHERE `user_id`='{$data['user_id']}'");
        //根据 商品id查 相应商品信息
        $shopinfo = $this->db->fetchRow("SELECT * FROM `shop` WHERE `shop_id`='{$data['shop_id']}'");
        //判断库存是否充足
        if ($shopinfo['num'] < $data['number']){
            $this->error = '库存不足!';
            return false;
        }
        $intinfo['number']??0;
        $neednum = $data['number'] * $shopinfo['money'];
        if ($intinfo['number'] < $neednum){
            $this->error = '对不起,您的积分不足!';
            return false;
        }

        //提交订单的时间
        $time = time();
        //生成订单号
        $number = uniqid(md5('Y-m-d'));
        //订单保存
        $this->db->execute("INSERT INTO `order_form` SET `number`='{$number}',`user_id`='{$data['user_id']}',`shop_id`='{$data['shop_id']}',`time`='{$time}',`num`='{$data['number']}',`needmoney`='{$neednum}'");



    }

    //后台处理订单
    public function deal($id){
        //查询订单信息
        $order_form_info = $this->db->fetchRow("SELECT * FROM `order_form` WHERE `order_form_id`='{$id}'");
        //查询该用户的积分
        $money = $this->db->fetchColumn("SELECT `number` FROM `integral` WHERE `user_id`='{$order_form_info['user_id']}'");
        //查出库存
        $num = $this->db->fetchColumn("SELECT `num` FROM `shop` WHERE `shop_id`='{$order_form_info['shop_id']}'");
        //扣除积分 计算
        $resultmoney = $money-$order_form_info['needmoney'];
        //扣除库存 计算
        $resultnum = $num-$order_form_info['num'];
        //获取修改时间
        $update_time = time();
        //获取管理员id
        @session_start();
        $member_id = $_SESSION['INFO']['member_id'];

        //开启事务
        $this->db->beginTransaction();

            //保存修改信息
        $result1 = $this->db->execute("UPDATE `order_form` SET `status`=1,`member_id`='{$member_id}',`update_time`='{$update_time}' WHERE `order_form_id`='{$id}'");

            //扣除积分
        $result2 = $this->db->execute("UPDATE `integral` SET `number`='{$resultmoney}' WHERE `integral_id`='{$order_form_info['user_id']}'");

            //扣除库存
        $reault3 = $this->db->execute("UPDATE `shop` SET `num`='{$resultnum}' WHERE `shop_id`='{$order_form_info['shop_id']}'");

        //处理

        if ($result1 && $result2 && $reault3) {

            $this->db->commit();  //全部成功，提交执行结果

            return ;

        } else {

            $this->db->rollback(); //有任何错误发生，回滚并取消执行结果

            $this->error = '处理订单失败!';

            return false;

        }



    }

    //后台取消订单
    public function cancel($id){
        //获取修改时间
        $update_time = time();
        //获取管理员id
        @session_start();
        $member_id = $_SESSION['INFO']['member_id'];
        //保存修改信息
        $this->db->execute("UPDATE `order_form` SET `status`=2,`member_id`='{$member_id}',`update_time`='{$update_time}' WHERE `order_form_id`='{$id}'");

    }

    //后台查看订单数据
    public function getOne($id){
        //根据 id 查询 单条数据
        $list = $this->db->fetchRow("SELECT * FROM `order_form` WHERE `order_form_id`='{$id}'");
        //查询会员名称
        $list['username'] = $this->db->fetchColumn("SELECT `username` FROM `users` WHERE `user_id`='{$list['user_id']}'");
        //查出处理人员
        $list['membername'] = $this->db->fetchColumn("SELECT `username` FROM `members` WHERE `member_id`='{$list['member_id']}'");
        //查询商品名称
        $list['shopname'] = $this->db->fetchColumn("SELECT `shop_name` FROM `shop` WHERE `shop_id`='{$list['shop_id']}'");

        return $list;

    }

    //后台删除 订单
    public function delete($id){
        //根据id 查 状态
        $status = $this->db->fetchColumn("SELECT `status` FROM `order_form` WHERE `order_form_id`='{$id}'");
        //判断是否处理了 订单
        if ($status == 0){
            $this->error = '该订单未处理!不能删除';
            return false;
        }
        //修改订单状态
        return $this->db->execute("UPDATE `order_form` SET `status`='-1' WHERE `order_form_id`='{$id}'");
    }

    //前台取消订单
    public function homecancel($id){
        $status = $this->db->fetchRow("SELECT * FROM `order_form` WHERE `order_form_id`='{$id}'");
        if($status == 1){
            $this->error = '已经发货,不能取消!';
        }

        //取消订单
        $this->db->execute("UPDATE `order_form` SET `status`=2,`member_id`=-1
 WHERE `order_form_id`='{$id}'");
    }

    //前台显示订单 列表
    public function indexhome($search=[],$page,$id){

        $where = " WHERE `status`!='-1' AND `user_id`='{$id}'";
        //判断是否搜索字段
        if (!empty($search)){
            $where = $where." AND ".implode(" AND ",$search);
        }
        //强转为数字
        $page = intval($page);
        //每页显示的条数
        $pagesize = 8;
        //总记录数
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `order_form`".$where." ORDER BY `order_form_id` DESC ");
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
        $list = $this->db->fetchAll("SELECT * FROM `order_form`".$where." ORDER BY `order_form_id` DESC ".$limit);
        //返回数组
        return ['list'=>$list,'count'=>$count,'pagesize'=>$pagesize,'totalpage'=>$totalpage,'page'=>$page];

    }

}