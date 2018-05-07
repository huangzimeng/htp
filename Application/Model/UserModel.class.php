<?php

//会员模型
class UserModel extends Model
{
    //注册会员
    public function register($data){
        //1.username不能为空
        if (empty($data['username'])){
            $this->error = "用户名不能为空!";
            return false;
        }
        //2.输入用户名.判断用户名是否存在
        $sql_count = "select count(*) from users where username='{$data['username']}'";
        $count = $this->db->fetchColumn($sql_count);
        if ($count>0){
            $this->error="用户名已经存在!";
            return false;
        }
        //3.realname不能为空
        if (empty($data['realname'])){
            $this->error = "真实姓名不能为空!";
            return false;
        }
        //4.telephone必须为数字
        if (!is_numeric($data['telephone'])){
            $this->error = "电话必须为数字!";
            return false;
        }
        //5.密码不能为空
        if (empty($data['password'])){
            $this->error = "密码不能为空!";
            return false;
        }
        //6.密码和确认密码必须一致
        if ($data['password']!=$data['repwd']){
            $this->error = "密码和确认密码必须一致!";
            return false;
        }
        //7.sex必须选择
        if (!isset($data['sex'])){
            $this->error = "请选择性别!";
            return false;
        }
        if (empty($data['photo'])){
            $data['photo'] = "./Uploads/Member/20180228/img_5a96120b9f330_50x50.jpg";
        }
        $password = md5($data['password']);

        //准备sql
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=htp;charset=utf8",'root','root');
        //准备sql
        $sql = "insert into users SET 
username='{$data['username']}',
realname='{$data['realname']}',
telephone='{$data['telephone']}',
password='{$password}',
sex='{$data['sex']}',
photo='{$data['photo']}'
";
        //执行sql
        $pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec($sql);
        $id = $pdo->lastInsertId();
        //执行
        $this->db->execute($sql);
        //添加id到积分表中
        $sql_e = "insert into integral set user_id='{$id}'";
        $this->db->execute($sql_e);
    }
    //会员登录
    public function check($data){
        $username = $data['username'];
        $password = md5($data['password']);
        //1.判断用户名是否正确
        $sql_db = "select * from users where username='{$username}'";
        $user_db = $this->db->fetchRow($sql_db);
        if (empty($user_db)){
            $this->error = "用户名错误!";
            return false;
        }
        //2.判断密码是否正确
        if ($password != $user_db['password']){
            $this->error = "用户名或者密码错误!";
            return false;
        }
        //返回用户信息
        return $user_db;
    }
    //验证home_id和home_pwd是否正确
    public function CheckIdpwd($home_id,$home_pwd){
        //根据 id查询用户信息
        $sql = "select * from users where user_id='{$home_id}'";
        $userinfo = $this->db->fetchRow($sql);
        if (empty($userinfo)){
            return false;
        }
        if ($userinfo['password'] != $home_pwd){
            return false;
        }
        //返回用户信息
        return $userinfo;
    }
    //修改个人资料回显
    public function Modify($user_id){
        //准备sql
        $sql = "select * from users where user_id='{$user_id}'";
        //执行sql
        return $this->db->fetchRow($sql);
    }
    //修改个人资料的保存
    public function ModifySave($data){
        //1.username不能为空
        if (empty($data['username'])){
            $this->error = "用户名不能为空!";
            return false;
        }
        //2.电话号码必须为数字
        if (!is_numeric($data['telephone'])){
            $this->error = "电话号码输入错误!";
            return false;
        }
        //3.必须选择sex
        if (!isset($data['sex'])){
            $this->error = "请选择性别!";
            return false;
        }
        //4.不上传头像使用默认值
        if (!isset($data['photo'])){
            $data['photo'] = "./Uploads/Member/20180228/img_5a96120b9f330_50x50.jpg";
        }
        //>>判断是否需要修改密码
        if (empty($data['oldpwd'])){//没有填写旧密码.不需要修改密码
            $sql = "update users set 
username='{$data['username']}',
telephone='{$data['telephone']}',
sex='{$data['sex']}',
photo='{$data['photo']}' where user_id='{$data['user_id']}'
";
        }else{//填写旧密码,需要修改密码
            //1.新密码不能为空
            if (empty($data['password'])){
                $this->error = "新密码不能为空!";
                return false;
            }
            //2.新密码必须和确认密码一致
            if ($data['password'] != $data['repwd']){
                $this->error = "新密码必须和确认密码一致";
                return false;
            }
            $password = md5($data['oldpwd']);
            //3.判断旧密码是否填写正确
            $sql_db="select password from users where user_id='{$data['user_id']}'";
            $a = $this->db->fetchRow($sql_db);
            //判断旧密码是否填写正确
            if ($password != $a['password']){
                $this->error = "旧密码输入错误!";
                return false;
            }
            $p = md5($data['password']);
            //准备sql
            $sql = "update users set 
username='{$data['username']}',
telephone='{$data['telephone']}',
sex='{$data['sex']}',
password='{$p}',
photo='{$data['photo']}' where user_id='{$data['user_id']}'
";
        }
        //执行sql
        $this->db->execute($sql);
    }
    //列表
    public function getAll(){
        return $this->db->fetchAll("SELECT * FROM `users`");
    }
    //后台添加会员
    public function addSave($data,$files){
        //判断信息是否输入完整
        if (empty($data['password']) || empty($data['repwd']) || empty($data['username']) || empty($data['realname']) || empty($data['telephone'])){
            $this->error = '会员信息填写不完整!';
            return false;
        }
        //判断电话号码是否为数字
        if (!is_numeric($data['telephone'])){
            $this->error = '电话号码必须为数字!';
            return false;
        }
        //判断两次密码是否一致
        if ($data['password'] != $data['repwd']){
            $this->error = '两次密码输入不正确!';
            return false;
        }
        //判断是否上传了图片
        if ($files['error'] == 4){  //不上传图片 添加会员
            //判断数据库中是否有相同的用户名
            $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `users` WHERE `username`='{$data['username']}'");
            if ($count > 0){
                $this->error = '用户名已存在!';
                return false;
            }
            //没有图片使用默认图片
            if (empty($data['photo'])){
                $data['photo'] = "./Uploads/Member/20180228/img_5a96120b9f330_50x50.jpg";
            }
            $password = md5($data['password']);
            //准备sql
            $sql_a = "insert into users SET 
username='{$data['username']}',
realname='{$data['realname']}',
telephone='{$data['telephone']}',
password='{$password}',
sex='{$data['sex']}',
photo='{$data['photo']}'
";
            $pdo = new PDO("mysql:host=127.0.0.1;dbname=htp;charset=utf8",'root','root');
            $pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $rs = $pdo->exec($sql_a);
            $id = $pdo->lastInsertId();
            $sql_in = "insert into `integral` set user_id='{$id}'";
            $this->db->execute($sql_in);
            //执行
            return $rs;
        }
        //需要上传图片
        //处理上传图片
        $upload = new UploadTool();
        $url = $upload->upload($files,'Users');
        if ($url === false){
            $this->error = $upload->getError();
            return false;
        }
        //生成缩略图
        $image = new ImageTool();
        $photo = $image->thumb($url,50,50);
        if ($photo === false){
            $this->error = $image->getError();
            return false;
        }
        //保存在data中
        $data['photo'] = $photo;

        //判断数据库中是否有相同的用户名
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `users` WHERE `username`='{$data['username']}'");
        if ($count > 0){
            $this->error = '用户名已存在!';
            return false;
        }
        //保存会员信息
        $pass = md5($data['password']);
        $sql_b = "INSERT INTO `users` SET `username`='{$data['username']}',`realname`='{$data['realname']}',`telephone`='{$data['telephone']}',`sex`='{$data['sex']}',`remark`='{$data['remark']}',`is_vip`='{$data['is_vip']}',`password`='{$pass}',`photo`='{$data['photo']}'";

        $pdo = new PDO("mysql:host=127.0.0.1;dbname=htp;charset=utf8",'root','root');
        $pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $rs = $pdo->exec($sql_b);
        $id = $pdo->lastInsertId();
        $sql_i = "insert into `integral` set user_id='{$id}'";
        $this->db->execute($sql_i);
        return $rs;
    }
    //查看会员信息
    public function getOne($id){
        return $this->db->fetchRow("SELECT * FROM users WHERE `user_id`='{$id}'");
    }
    //修改会员信息
    public function edit($data){
        return $this->db->execute("UPDATE `users` SET `remark`='{$data['remark']}' WHERE `user_id`='{$data['id']}'");
    }
    //删除会员
    public function delete($id){
        //检测该会员是够消费过
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `histories` WHERE `user_id`='{$id}'");
        if ($count > 0){
            $this->error = '消费过的会员不能删除!';
            return false;
        }
        //删除会员
        return $this->db->execute("DELETE FROM `users` WHERE `user_id`='{$id}'");
    }
    //充值服务
    public function Recharge($data){
        //1.金额不能为空
        if (empty($data['money'])){
            $this->error = "金额不能为空!";
            return false;
        }
        //2.金额必须为数字
        if (!is_numeric($data['money'])){
            $this->error = "金额必须为数字!";
            return false;
        }
        //3.金额不能小于0
        if ($data['money']<0){
            $this->error = "金额不能小于0";
            return false;
        }
        $money = $data['money'];//充值的金额
        //>>升级vip
            //根据金额查询对应的vip等级
        $sql_w = "select * from `vip` where `condition`<=$money order by `condition` DESC limit 1";
        $w = $this->db->fetchRow($sql_w);
        if (empty($w)){//$w为空,表示之前没有会员等级
            $vip=0;
        }else{//充值金额对应的vip等级
            $vip = $w['rating'];
        }
            //查询用户原有的vip等级
        $sql_e = "select is_vip from `users` where user_id='{$data['user_id']}'";
        $e=$this->db->fetchColumn($sql_e);
//        var_dump($e);echo "<br>";
        if ($e>=$vip){//原有vip等级高于现在vip充值后的vip等级
            $vip = $e;
        }
        $sql_vip = "update `users` set `is_vip`='{$vip}' where user_id='{$data['user_id']}'";
        $this->db->execute($sql_vip);

        //>>充值赠送
        //赠送的金额,根据规定的规则,满多少送多少!
        $sql_q = "select * from `rule` where remoney<=$money order by remoney DESC limit 1";
        $q = $this->db->fetchRow($sql_q);
        //送的金额
        if (empty($q)){//没有达到赠送的条件,不赠送
            $sendmoney=0;
        }else{
            $sendmoney=$q['sendmoney'];
        }
        //准备sql
        $sql = "update users set money=money+'{$data['money']}'+'{$sendmoney}' where user_id='{$data['user_id']}'";
        //执行
        $this->db->execute($sql);
        //充值成功!添加一条日志
        $member_id = $_SESSION['INFO']['member_id'];
        $time = time();
        $content = "充值".$data['money']."赠送".$sendmoney."vip等级变更为:".$vip;
        $sql_d = "select money from users where user_id='{$data['user_id']}'";
        $remainder = $this->db->fetchColumn($sql_d);
        $sql_in = "insert into histories set
user_id='{$data['user_id']}',
member_id='{$member_id}',
`type`=0,
amount='{$data['money']}',
content='{$content}',
`time`='{$time}',
remainder='{$remainder}'
";
        //执行
        $this->db->execute($sql_in);
    }
    //会员消费
    public function Consumption($data)
    {
        //1.查询会员余额
        $sql_e = "select money from users where user_id='{$data['user_id']}'";
        $f = $this->db->fetchColumn($sql_e);
        //2.查询消费金额
        $sql_b = "select money from plans where plan_id='{$data['plan']}'";
        $b = $this->db->fetchColumn($sql_b);
        //3.判断是否使用代金券抵用
        if (!empty($data['code'])) {
            //.查询代金券金额
            $sql_c = "select money from codes where code='{$data['code']}'";
            $c = $this->db->fetchColumn($sql_c);
            if (!is_numeric($c)) {
                $this->error = "代金券码错误!";
                return false;
            }
            //代金券多次使用
            if ($c >= $b) {
                $sql_u = "update `codes` set money=money-$b where code='{$data['code']}'";
                $this->db->execute($sql_u);
            }
        } else {
            $c = 0;
        }

        //4.查询vip打折金额
        $sql_g = "select is_vip from users where user_id='{$data['user_id']}'";
        $g = $this->db->fetchColumn($sql_g);
            //根据vip等级查询打折力度
        $sql_t = "select discount from vip where rating='{$g}'";
        $t = $this->db->fetchColumn($sql_t);//打折数

        //5.会员的余额必须大于套餐金额才能消费。
        if ($f<$b){
            $this->error = "余额不足,请充值!";
            return false;
        }
        //判断代金券的金额大于消费金额的情况
        if ($g==='0'){
            $e=$b-$c;
        }else{
            $e=(string)($b*$t)-$c;//真实消费的金额
        }
        if ($e>0){
            $sql = "update users set money=money-'{$e}' where user_id='{$data['user_id']}'";
            $this->db->execute($sql);
        }else{
            $sql = "update users set money=money-0 where user_id='{$data['user_id']}'";
            $this->db->execute($sql);
        }
        //消费成功!获得积分!
            //判断是否是第一次消费
        $sql_t = "select * from `integral` where user_id='{$data['user_id']}'";
        $t=$this->db->fetchRow($sql_t);
        if (empty($t)){//第一次消费
            $sql_o = "insert into `integral` set `number`='{$e}',`user_id`='{$data['user_id']}'";
            $this->db->execute($sql_o);
        }
        //第二次消费
        $sql_p="update `integral` set `number`=`number`+'{$e}' where user_id = '{$data['user_id']}'";
        $this->db->execute($sql_p);

        //消费成功!保存一份日志
        $member_id = $data['member'];
        $time = time();
        $content = "消费".$e.","."赠送积分:".$e;
        $sql_d = "select money from users where user_id='{$data['user_id']}'";
        $remainder = $this->db->fetchColumn($sql_d);
        $sql_in = "insert into histories set
user_id='{$data['user_id']}',
member_id='{$member_id}',
`type`=1,
amount='{$e}',
content='{$content}',
`time`='{$time}',
remainder='{$remainder}'
";
        //执行
        $this->db->execute($sql_in);
    }
    //搜索 + 分页
    public function getpage($search,$page){
        $where = '';
        //判断是否搜索字段
        if (!empty($search)){
            $where = " WHERE ".implode(" AND ",$search);
        }
        //强转为数字
        $page = intval($page);
        //每页显示的条数
        $pagesize = 8;
        //总记录数
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `users`".$where);
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
        $list = $this->db->fetchAll("SELECT * FROM `users`".$where.$limit);
        //返回数组
        return ['list'=>$list,'count'=>$count,'pagesize'=>$pagesize,'totalpage'=>$totalpage,'page'=>$page];
    }
    //获取会员总人数(后台首页显示)
    public function count(){
        return $this->db->fetchColumn("SELECT COUNT(*) FROM `users`");
    }
    //获取总充值数(后台首页显示)
    public function Allin(){
        $sql = "select sum(amount) as Allin from `histories` where `type`=0";
         return $this->db->fetchRow($sql);
    }
    //获取总消费金额(后台显示)
    public function Allout(){
        $sql = "select sum(amount) as Allout from `histories` where `type`=1";
        return $this->db->fetchRow($sql);
    }

}