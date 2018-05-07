<?php
//>>6.设置header头
    header("Content-Type: text/html; charset=UTF-8");
//>>5.将项目中的所有的路径定义以常量来表示 都以 / 结尾
    defined("DS") or define("DS",DIRECTORY_SEPARATOR);//定义目录分隔符
//    defined("ROOT_PATH") or define("ROOT_PATH",__DIR__.DS);//项目的根目录
    defined("ROOT_PATH") or define("ROOT_PATH",dirname($_SERVER['SCRIPT_FILENAME']).DS);//项目的根目录
    defined("APP_PATH") or define("APP_PATH",ROOT_PATH."Application".DS);//Application目录所在路径
    defined("FRAME_PATH") or define("FRAME_PATH",ROOT_PATH."Framework".DS);//Framework目录所在路径
    defined("PUBLIC_PATH") or define("PUBLIC_PATH",ROOT_PATH."Public".DS);//Public目录所在路径
    defined("UPLOADS_PATH") or define("UPLOADS_PATH",ROOT_PATH."Uploads".DS);//Uploads目录所在路径
    defined("CONFIG_PATH") or define("CONFIG_PATH",APP_PATH."Config".DS);//Config目录所在路径
    defined("CONTROLLER_PATH") or define("CONTROLLER_PATH",APP_PATH."Controller".DS);//Controller目录所在路径
    defined("MODEL_PATH") or define("MODEL_PATH",APP_PATH."Model".DS);//Model目录所在路径
    defined("VIEW_PATH") or define("VIEW_PATH",APP_PATH."View".DS);//View目录所在路径
    defined("TOOLS_PATH") or define("TOOLS_PATH",FRAME_PATH."Tools".DS);//VTools目录所在路径

//>>4.应用配置文件
    $GLOBALS['config'] = require CONFIG_PATH."application.config.php";
//>>3.接收参数
    $p = $_GET['p']??$GLOBALS['config']['app']['default_platform'];//代表平台
    $c = $_GET['c']??$GLOBALS['config']['app']['default_controller'];//代表控制器的类名
    $a = $_GET['a']??$GLOBALS['config']['app']['default_action'];//方法的名称

    /**
     * 定义p,c,a 常量
     */
    defined("PLATFORM") or define("PLATFORM",$p);
    defined("CONTROLLER") or define("CONTROLLER",$c);
    defined("ACTION") or define("ACTION",$a);

    /**
     * 当前访问的控制器所在路径
     * 当前视图文件所在的路径
     */
    defined("CURRENT_CONTROLLER_PATH") or define("CURRENT_CONTROLLER_PATH",CONTROLLER_PATH.$p.DS);//当前访问的控制器所在路径
    defined("CURRENT_VIEW_PATH") or define("CURRENT_VIEW_PATH",VIEW_PATH.$p.DS.$c.DS);//当前访问的控制器所在路径

//>>1.创建控制器类对象
    //拼写类名
    spl_autoload_register('autoload');
    $class_name = $c."Controller";
    $controller = new $class_name();
//>>2.调用方法
    $controller->$a();


    /**
     * 类的自动加载
     * @param $class_name 类名
     */
    function autoload($class_name){
        //保存没有规律的类的类名和其路径的映射关系
        $classMapping = [
            "Model"=>FRAME_PATH."Model.class.php",
            "Controller"=>FRAME_PATH."Controller.class.php",
            "DB"=>TOOLS_PATH."DB.class.php",
            "PHPExcel"=>FRAME_PATH."PHPExcel/Classes/PHPExcel.php",
        ];

        //判断类自动加载
        if(isset($classMapping[$class_name])){//判断没有规律的类
            require $classMapping[$class_name];
        }elseif (substr($class_name,-10) == "Controller"){//判断是否为控制器
            require CURRENT_CONTROLLER_PATH."{$class_name}.class.php";
        }elseif (substr($class_name,-5) == "Model"){//加载模型类
            require MODEL_PATH."{$class_name}.class.php";
        }elseif (substr($class_name,-4) == "Tool"){//加载工具类
            require TOOLS_PATH."{$class_name}.class.php";
        }
    }
