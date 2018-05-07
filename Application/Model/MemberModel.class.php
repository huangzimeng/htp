<?php

//管理员模型
class MemberModel extends Model
{
    //获取所有数据
    public function getAll(){
        //准备sql
        $sql = "select * from members";
        //执行sql
        $rows = $this->db->fetchAll($sql);
        foreach ($rows as &$v){
            $sql = "select `name` from `group` where group_id='{$v['group_id']}'";
            $a = $this->db->fetchColumn($sql);
            $v['group'] = $a;
        }
        return $rows;
    }
    //获取分组数据
    public function getGroup(){
        //准备sql
        $sql = "select *  from  `group`";
        //执行
        return $this->db->fetchAll($sql);
    }
    //插入数据
    public function Insert($data){
        //1.姓名不能为空
        if (empty($data['username'])){
            $this->error = "姓名不能为空!";
            return false;
        }
        //2.真实姓名不能为空
        if (empty($data['realname'])){
            $this->error = "真实姓名不能为空";
            return false;
        }
        //3.电话号码必须为数字
        if (!is_numeric($data['telephone'])){
            $this->error = "电话号码必须为数字!";
            return false;
        }
        //4.密码不能为空
        if (empty($data['password'])){
            $this->error = "密码不能为空!";
            return false;
        }
        //5.密码和确认密码必须一致
        if ($data['password'] != $data['repwd']){
            $this->error = "密码和确认密码必须一致!";
            return false;
        }
        //7.请选择性别
        if (!isset($data['sex'])){
            $this->error = "请选择性别";
            return false;
        }
        //8.请选择是否是管理员
        if (!isset($data['is_admin'])){
            $this->error = "请选择是否是管理员";
            return false;
        }
        //6.没有上传头像使用默认值
        if (empty($data['photo'])){
            $data['photo'] = "./Uploads/Member/20180228/img_5a958c93f069b_50x50.jpg";
        }
        $password = md5($data['password']);
        //准备sql
        $sql = "insert into members set
username='{$data['username']}',
password='{$password}',
realname='{$data['realname']}',
sex='{$data['sex']}',
telephone='{$data['telephone']}',
group_id='{$data['group_id']}',
is_admin='{$data['is_admin']}',
photo='{$data['photo']}'
";
        //执行sql
        $this->db->execute($sql);
    }
    //修改回显
    public function Edit($member_id){
        //准备sql
        $sql = "select * from members where member_id = '{$member_id}'";
        //执行sql
        return $this->db->fetchRow($sql);
    }
    //修改保存
    public function EditSave($data){
        //1.姓名不能为空
        if (empty($data['username'])){
            $this->error = "姓名不能为空!";
            return false;
        }
        //2.真实姓名不能为空
        if (empty($data['realname'])){
            $this->error = "真实姓名不能为空";
            return false;
        }
        //3.电话号码必须为数字
        if (!is_numeric($data['telephone'])){
            $this->error = "电话号码必须为数字!";
            return false;
        }
        //4.请选择性别
        if (!isset($data['sex'])){
            $this->error = "请选择性别";
            return false;
        }
        //5.请选择是否是管理员
        if (!isset($data['is_admin'])){
            $this->error = "请选择是否是管理员";
            return false;
        }
        //判断是否修改头像
        if (empty($data['photo'])) {//不需要修改头像
            if (!empty($data['oldpwd'])) {//填写旧密码,需要修改密码
                //1.新密码不能为空
                if (empty($data['password'])) {
                    $this->error = "新密码不能为空";
                    return false;
                }
                //2.新密码必须和旧密码一致
                if ($data['password'] != $data['repwd']) {
                    $this->error = "新密码必须和旧密码一致";
                    return false;
                }
                //3.旧密码必须填写正确
                $sql_db = "select password from members where member_id='{$data['member_id']}'";
                $pwd = $this->db->fetchColumn($sql_db);
                if ($pwd != md5($data['oldpwd'])) {
                    $this->error = "旧密码填写错误!";
                    return false;
                }
                $password = md5($data['password']);
                //准备sql
                $sql = "update members set 
username='{$data['username']}',
realname='{$data['realname']}',
password='{$password}',
sex='{$data['sex']}',
telephone='{$data['telephone']}',
group_id='{$data['group_id']}',
is_admin='{$data['is_admin']}' where member_id='{$data['member_id']}'
";
            } else {//没有填写旧密码,不需要修改密码
                //准备sql
                $sql = "update members set 
username='{$data['username']}',
realname='{$data['realname']}',
sex='{$data['sex']}',
telephone='{$data['telephone']}',
group_id='{$data['group_id']}',
is_admin='{$data['is_admin']}' where member_id='{$data['member_id']}'
";
            }

        }else{//需要修改头像
            if (!empty($data['oldpwd'])) {//填写旧密码,需要修改密码
                //1.新密码不能为空
                if (empty($data['password'])) {
                    $this->error = "新密码不能为空!";
                    return false;
                }
                //2.新密码必须和确认密码一致
                if ($data['password'] != $data['repwd']) {
                    $this->error = "新密码必须和确认密码一致!";
                    return false;
                }
                //4.新密码不能与旧密码相同
                if ($data['oldpwd'] = $data['password']){
                    $this->error = "新密码不能与旧密码相同";
                    return false;
                    echo 1;
                    die;
                }
                //3.旧密码必须填写正确
                $sql_db = "select password from members where member_id='{$data['member_id']}'";
                $pwd = $this->db->fetchColumn($sql_db);
                if ($pwd != md5($data['oldpwd'])) {
                    $this->error = "旧密码填写错误!";
                    return false;
                }
                $password = md5($data['password']);
                //准备sql
                $sql = "update members set 
username='{$data['username']}',
realname='{$data['realname']}',
password='{$password}',
photo='{$data['photo']}',
sex='{$data['sex']}',
telephone='{$data['telephone']}',
group_id='{$data['group_id']}',
is_admin='{$data['is_admin']}' where member_id='{$data['member_id']}'
";
            } else {//没有填写旧密码,不需要修改密码
                //准备sql
                $sql = "update members set 
username='{$data['username']}',
realname='{$data['realname']}',
sex='{$data['sex']}',
photo='{$data['photo']}',
telephone='{$data['telephone']}',
group_id='{$data['group_id']}',
is_admin='{$data['is_admin']}' where member_id='{$data['member_id']}'
";
            }
        }
        //执行sql
        $this->db->execute($sql);
}
    //删除
    public function Delete($member_id){
        //判断删除员工是否有服务
        $sql_c = "select * from `order` where barber='{$member_id}'";
        $a = $this->db->fetchRow($sql_c);
        if (!empty($a)){
            $this->error = "不能删除有服务的员工!";
            return false;
        }
        $sql = "delete from `members` where member_id='{$member_id}'";
        $this->db->execute($sql);
    }
    //登录信息检测
    public function check($data){
        //验证码检测
        if (empty($data['captcha'])){
            $this->error = '请输入验证码!';
            return false;
        }
        //检测验证码是否正确
        //>>开启session
        @session_start();
        if (strtoupper($_SESSION['CAPTCHA']) != strtoupper($data['captcha'])){
            $this->error = '验证码错误!';
            return false;
        }
        //验证码正确后 更加username 查询数据
        $sql = "SELECT * FROM members WHERE (`username`='{$data['username']}' AND `is_admin`=1)";
        $memberinfo = $this->db->fetchRow($sql);
        //判断是否有数据
        if (empty($memberinfo)){
            $this->error = '该管理员不存在!';
            return false;
        }
        //有数据 判断密码是否正确
        $password = md5($data['password']);
        if ($memberinfo['password'] != $password){
            $this->error = '密码错误!';
            return false;
        }
        //密码正确 将用户所有信息保存在session中
        $_SESSION['INFO'] = $memberinfo;
        //是否记住登录
        if (isset($data['remember'])){
            //将查询出来的密码再次加密 保存
            $pass = md5($memberinfo['password'].'member');
            setcookie('id',$memberinfo['member_id'],time()+7*24*3600,'/');
            setcookie('pass',$pass,time()+7*24*3600,'/');
        }
        //将 登录时间 和登录ip 更新到数据库
        $last_login = time();
        $last_login_ip = ip2long($_SERVER['REMOTE_ADDR']);
        $sql = "UPDATE members SET `last_login`='{$last_login}',`last_login_ip`='{$last_login_ip}' WHERE `member_id`='{$memberinfo['member_id']}'";
        $this->db->execute($sql);
    }
    //自动登录 信息检测
    public function checkIdPwd($id,$pass){
        //根据id 查询数据
        $sql = "SELECT * FROM members WHERE `member_id`='{$id}'";
        $memberinfo = $this->db->fetchRow($sql);
        //没有查询出数据
        if (empty($memberinfo)){
            return false;
        }
        //查询出数据
        $password = md5($memberinfo['password'].'member');
        if ($password != $pass){
            return false;
        }
        //密码正确
        return $memberinfo;
    }
    //获取美发师员工信息
    public function getBarber(){
        //准备sql
        $sql = "select * from members where is_admin=0";
        //执行
        return $this->db->fetchAll($sql);
    }
    //获取美发师member_id和username
    public function getMandU(){
        //准备sql
        $sql = "select member_id,username from members where is_admin=0";
        //执行sql
        return  $this->db->fetchAll($sql);
    }

    //搜索 + 分页
    public function getpage($search=[],$page){
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
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `members`".$where);
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
        $list = $this->db->fetchAll("SELECT * FROM `members`".$where.$limit);
        foreach ($list as &$v){
            $sql = "select `name` from `group` where group_id='{$v['group_id']}'";
            $a = $this->db->fetchColumn($sql);
            $v['group'] = $a;
        }
        //返回数组
        return ['list'=>$list,'count'=>$count,'pagesize'=>$pagesize,'totalpage'=>$totalpage,'page'=>$page];
    }

    //后台首页修改个人信息
    public function update($data,$files){
        @session_start();
        //判断基本信息
        if (empty($data['realname']) || empty($data['telephone'])){
            $this->error = '基本信息不能为空!';
            return false;
        }
        if (!is_numeric($data['telephone'])){
            $this->error = '电话号码必须为数字!';
            return false;
        }
        //不修改密码
        if (empty($data['oldpwd'])){
            if (!empty($data['newpwd'] || !empty($data['repwd']))){
                $this->error = '修改密码请先输入原密码';
                return false;
            }
            //判断是否修改头像
            if ($files['error'] == 4){      //没有头像上传 直接保存基本信息
                $this->db->execute("UPDATE `members` SET `realname`='{$data['realname']}',`telephone`='{$data['telephone']}',`sex`='{$data['sex']}' WHERE `member_id`='{$_SESSION['INFO']['member_id']}'");
                //更新session
                $_SESSION['INFO'] = $this->db->fetchRow("SELECT * FROM `members` WHERE `member_id`='{$_SESSION['INFO']['member_id']}'");
                return;
            }else{
                //需要上传图片  处理图片
                $uploadtool = new UploadTool();
                $url = $uploadtool->upload($files,'Member');
                if ($url === false){
                    $this->error = $uploadtool->getError();
                    return false;
                }
                $imagetool = new ImageTool();
                $photo = $imagetool->thumb($url,50,50);
                if ($photo === false){
                    $this->error = $imagetool->getError();
                    return false;
                }
                //保存 基本信息+图片
                $this->db->execute("UPDATE `members` SET `realname`='{$data['realname']}',`telephone`='{$data['telephone']}',`photo`='{$photo}',`sex`='{$data['sex']}' WHERE `member_id`='{$_SESSION['INFO']['member_id']}'");
                //更新session
                $_SESSION['INFO'] = $this->db->fetchRow("SELECT * FROM `members` WHERE `member_id`='{$_SESSION['INFO']['member_id']}'");
                return;
            }
        }
        //需要修改密码
        else{
            if (empty($data['newpwd']) || empty($data['repwd'])){
                $this->error = '修改密码时请填写完整!';
                return false;
            }
            if ($data['newpwd'] != $data['repwd']){
                $this->error = '两次密码输入不一致!';
                return false;
            }

            //判断原密码是否正确
            $oldpassword = $this->db->fetchColumn("SELECT `password` FROM `members` WHERE `member_id`='{$_SESSION['INFO']['member_id']}'");
            //加密输入的旧密码
            $oldpwd = md5($data['oldpwd']);
            //判断密码是否正确
            if ($oldpassword != $oldpwd){
                $this->error = '原密码输入错误!';
                return false;
            }
            //加密新密码
            $password = md5($data['newpwd']);
            if ($password == $oldpassword){
                $this->error = '新密码不能与原密码一样~';
                return false;
            }

            if ($files['error'] == 4){
                //保存信息
                $this->db->execute("UPDATE `members` SET `realname`='{$data['realname']}',`telephone`='{$data['telephone']}',`password`='{$password}',`sex`='{$data['sex']}' WHERE `member_id`='{$_SESSION['INFO']['member_id']}'");
                //更新session
                $_SESSION['INFO'] = $this->db->fetchRow("SELECT * FROM `members` WHERE `member_id`='{$_SESSION['INFO']['member_id']}'");
                return;

            }else{
                //需要上传图片  处理图片
                $uploadtool = new UploadTool();
                $url = $uploadtool->upload($files,'Member');
                if ($url === false){
                    $this->error = $uploadtool->getError();
                    return false;
                }
                $imagetool = new ImageTool();
                $photo = $imagetool->thumb($url,50,50);
                if ($photo === false){
                    $this->error = $imagetool->getError();
                    return false;
                }
                //保存信息
                $this->db->execute("UPDATE `members` SET `realname`='{$data['realname']}',`telephone`='{$data['telephone']}',`photo`='{$photo}',`password`='{$password}',`sex`='{$data['sex']}' WHERE `member_id`='{$_SESSION['INFO']['member_id']}'");
                //更新session
                $_SESSION['INFO'] = $this->db->fetchRow("SELECT * FROM `members` WHERE `member_id`='{$_SESSION['INFO']['member_id']}'");
                return;
            }
        }

    }


}