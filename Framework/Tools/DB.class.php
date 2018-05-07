<?php

//>>3.设计一个DB类
class DB{
    //设置一些属性,保存数据库的连接信息
    private $host;//主机
    private $username;//用户名
    private $password;//密码
    private $database;//数据库名
    private $port;//端口号
    private $charset;//字符集

    //保存数据库连接
    private $link;

    //私有的静态的属性 用于保存创建好的对象
    private static $instance;
    /**
     * 构造方法,用于初始化
     * @param $config 配置信息
     */
    private function __construct($config=[])
    {
        //如果没有传配置信息就使用配置文件中的配置信息
        //属性的初始化
        $this->host = $config['host']??'127.0.0.1';
        $this->username = $config['username']??'root';
        $this->password = $config['password'];
        $this->database = $config['database'];
        $this->port = $config['port']??3306;
        $this->charset = $config['charset']??'utf8';
        //1.连接数据库
        $this->connect();
        //2.设置字符集
        $this->setCharset();
    }
    //私有的克隆方法
    private function __clone(){}
    //公有的静态的创建对象的方法
    public static function getInstance($config=[]){
        //如果没有创建过对象才创建,如果创建过就直接返回对象
        if(!self::$instance instanceof self){//不是当前类的实例
            self::$instance = new self($config);
        }
        //返回创建好的对象
        return self::$instance;
    }
    /**
     * 连接数据库
     */
    private function connect(){
        //>>1.连接数据库
        $this->link = @mysqli_connect($this->host,$this->username,$this->password,$this->database,$this->port);
//        dump($this);
        if($this->link === false){//连接失败
            //提示连接失败的错误信息,退出
            die(
                "连接数据库失败!<br/>".
                "错误信息:".mysqli_connect_error()."<br/>".
                "错误编号:".mysqli_connect_errno()."<br/>"
            );
        }
    }

    /**
     * 设置字符集
     */
    private function setCharset(){
        //设置字符集
        $rs = mysqli_set_charset($this->link,$this->charset);
        if($rs === false){//设置字符集失败
            //提示连接失败的错误信息,退出
            die(
                "设置字符集失败!<br/>".
                "错误信息:".mysqli_error($this->link)."<br/>".
                "错误编号:".mysqli_errno($this->link)."<br/>"
            );
        }
    }
    /**
     * 执行sql语句
     * @param $sql 需要执行的sql语句
     * @return 结果集或者boolean
     */
    private function query($sql){
        //3.执行sql
        $result = mysqli_query($this->link,$sql);
        if($result === false){
            die(
                "执行sql语句失败!<br/>".
                "错误信息:".mysqli_error($this->link)."<br/>".
                "错误编号:".mysqli_errno($this->link)."<br/>".
                "错误的SQL:" . $sql
            );
        }
        //4.返回结果
        return $result;
    }

    /**
     * 专业用于执行 执行类的sql语句 ,例如 update insert delete
     * @param sql
     * @return boolean
     */
    public function execute($sql){
        //执行sql语句
            return $this->query($sql);
    }

    /**
     * 用于执行sql语句,返回二维数组
     * @param $sql 需要执行的sql语句
     * @return [] 二维数组|如果没有返回空数组
     */
    public function fetchAll($sql){
        //>>1.执行sql语句
            $result = $this->query($sql);
        //>>2.解析结果集,得到二维数组
            $rows = mysqli_fetch_all($result,MYSQLI_ASSOC);//关联数组
        //>>3.返回二维数组
            return $rows;
    }

    /**
     * 用于执行sql语句,返回一维关联数组
     * @param $sql 需要执行的sql语句
     * @return [] 一维关联数组 | 返回空数组
     */
    public function fetchRow($sql){
/*        //>>1.执行sql语句
            $result = $this->query($sql);
        //>>2.解析结果 得到一维数组
            $row = mysqli_fetch_assoc($result);
        //>>3.返回数组
            return $row??[];*/

        //>>1.执行sql语句
            $rows = $this->fetchAll($sql);
        //>>2.解析结果 得到一维数组
            $row = !empty($rows) ? $rows[0]:[];
        //>>3.返回数组
            return $row;
    }

    /**
     * 用于执行sql语句,返回第一行第一列的   值
     * @param $sql 需要执行的sql
     * @return mixed 返回一个值
     */
    public function fetchColumn($sql){
/*        //>>1.执行sql语句
            $result = $this->query($sql);
        //>>2.解析结果,得到第一行第一列的值
            $row = mysqli_fetch_row($result);
        //>>3.返回值
            return $row[0];*/
        //>>1.执行sql语句
            $row = $this->fetchRow($sql);
        //>>2.解析结果,得到第一行第一列的值
        //>>3.返回值
//            return array_pop($row);
//            return empty($row)?null:array_values($row)[0];
        return array_shift($row);
    }
    /**
     * 析构方法,清理对象占用的资源
     */
    public function __destruct(){
        mysqli_close($this->link);
    }

    /**
     * 当对象被序列化的时候自动调用,返回需要被序列化的属性名称组成的数组
     * __sleep()
     */
    public function __sleep()
    {
        return ['host','username','password','database','port','charset'];
    }

    /**
     * 当对象被反序列化的时候自动调用,用于重新初始化操作
     */
    public function __wakeup()
    {
        //连接数据库
        $this->connect();
        //设置字符集
        $this->setCharset();
    }

    //开启事务
    public function beginTransaction(){
        $this->link->autocommit(FALSE);
    }
    //提交事务
    public function commit(){
        $this->link->commit();
    }
    //回滚事务
    public function rollback(){
        $this->link->rollback();
    }


}