<?php

//配置信息
return [
    'db'=>[//数据库的连接信息
        'host'=>'127.0.0.1',
        'username'=>'root',
        'password'=>'root',
        'database'=>'htp',
        'port'=>3306,
        'charset'=>'utf8'
    ],
    'app'=>[//默认的访问参数
        'default_platform'=>'Home',
        'default_controller'=>'Index',
        'default_action'=>'index',
    ]
];
