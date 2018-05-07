<?php

/**
 * 积分商城 模型
 */
class ShopModel extends Model
{
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
        $pagesize = 10;
        //总记录数
        $count = $this->db->fetchColumn("SELECT COUNT(*) FROM `shop`".$where);
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
        $list = $this->db->fetchAll("SELECT * FROM `shop`".$where.$limit);
        //返回数组
        return ['list'=>$list,'count'=>$count,'pagesize'=>$pagesize,'totalpage'=>$totalpage,'page'=>$page];

    }

    //添加商品
    public function add($data,$files){
        //判断信息完整性
        if (empty($data['shop_name']) || empty($data['intro']) || empty($data['money']) || empty($data['num'])){
            $this->error = '请填写完整信息!';
            return false;
        }
        if (!is_numeric($data['num'])){
            $this->error = '库存必须为数字!';
            return false;
        }
        //图片是否上传
        if ($files['error'] == 4){
            $this->error = '商品必须上传图片!';
            return false;
        }
        //处理图片
        $upload = new UploadTool();
        $url = $upload->upload($files,'Shop');
        if ($url === false){
            $this->error = $upload->getError();
            return false;
        }
        //缩略图
        $image = new ImageTool();
        $photo = $image->thumb($url,214,153);
        if ($photo === false){
            $this->error = $image->getError();
            return false;
        }
        //保存商品信息
        return $this->db->execute("INSERT INTO `shop` SET `shop_name`='{$data['shop_name']}',`intro`='{$data['intro']}',`money`='{$data['money']}',`status`='{$data['status']}',`photo`='{$photo}',`num`='{$data['num']}'");

    }

    //回显商品信息
    public function getOne($id){
        return $this->db->fetchRow("SELECT * FROM `shop` WHERE `shop_id`='{$id}'");
    }

    //修改商品
    public function edit($data,$files){
        //判断信息完整性

        if (empty($data['shop_name']) || empty($data['intro']) || empty($data['money']) || empty($data['num'])){
            $this->error = '请填写完整信息!';
            return false;
        }
        if (!is_numeric($data['num'])){
            $this->error = '库存必须为数字!';
            return false;
        }

        //判断是否修改图片
        if ($files['error'] == 4){  //不修改图片
            return $this->db->execute("UPDATE `shop` SET `shop_name`='{$data['shop_name']}',`intro`='{$data['intro']}',`money`='{$data['money']}',`status`='{$data['status']}',`num`='{$data['num']}' WHERE `shop_id`='{$data['id']}'");
        }else{  //需要修改图片
            //处理图片
            $upload = new UploadTool();
            $url = $upload->upload($files,'Shop');
            if ($url === false){
                $this->error = $upload->getError();
                return false;
            }
            //缩略图
            $image = new ImageTool();
            $photo = $image->thumb($url,214,153);
            if ($photo === false){
                $this->error = $image->getError();
                return false;
            }
            //保存修改信息
            return $this->db->execute("UPDATE `shop` SET `shop_name`='{$data['shop_name']}',`intro`='{$data['intro']}',`money`='{$data['money']}',`status`='{$data['status']}',`num`='{$data['num']}',`photo`='{$photo}' WHERE `shop_id`='{$data['id']}'");
        }
    }

    //删除商品
    public function delete($id){
        //判断需要删除的商品 是否在上架
        $status = $this->db->fetchColumn("SELECT `status` FROM `shop` WHERE `shop_id`='{$id}'");
        if ($status == 1){
            $this->error = '该商品正在上架,不能删除!';
            return false;
        }
        //删除未上架的商品
        return $this->db->execute("DELETE FROM `shop` WHERE `shop_id`='{$id}'");
    }



}