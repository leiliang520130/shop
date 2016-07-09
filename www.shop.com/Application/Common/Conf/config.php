<?php
define('BASE_URL','http://www.shop.com');
return array(
    //'配置项'=>'配置值'
    'URL_MODEL'=>2,
    'TMPL_PARSE_STRING'=>[
        '__CSS__'=> BASE_URL . '/Public/Home/css',
        '__JS__'=> BASE_URL . '/Public/Home/js',
        '__IMG__'=> BASE_URL . '/Public/Home/images',
        '__UPLOADIFY__'=> BASE_URL . '/Public/Home/ext/uploadify',
        '__LAYER__'=> BASE_URL . '/Public/Home/ext/layer',
        '__ZTREE__'=> BASE_URL . '/Public/Home/ext/ztree',
        '__TREEGRID__'=> BASE_URL . '/Public/Home/ext/treegrid',
        '__UEDITOR__'=> BASE_URL . '/Public/Home/ext/ueditor',
        '__JQUERY_VALIDATE__'=> BASE_URL . '/Public/Home/ext/jquery-validate',
    ],

    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'tpshop',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '123456',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  '',    // 数据库表前缀
    'DB_PARAMS'          	=>  array(), // 数据库连接参数
    'DB_DEBUG'  			=>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       =>  false,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        =>  0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        =>  false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         =>  1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           =>  '', // 指定从服务器序号
    //分页相关的配置
    'PAGE_SETTING'=>[
        'PAGE_SIZE'=>20,
        'PAGE_THEME'=>'%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    ],

    'COOKIE_PREFIX'=>'www_shop_com_',

    //Redis Session配置
    'SESSION_AUTO_START'	=>  true,	// 是否自动开启Session
    'SESSION_TYPE'			=>  'Redis',	//session类型
    'SESSION_PERSISTENT'    =>  1,		//是否长连接(对于php来说0和1都一样)
    'SESSION_CACHE_TIME'	=>  1,		//连接超时时间(秒)
    'SESSION_EXPIRE'		=>  0,		//session有效期(单位:秒) 0表示永久缓存
    'SESSION_PREFIX'		=>  'sess_',		//session前缀
    'SESSION_REDIS_HOST'	=>  '127.0.0.1', //分布式Redis,默认第一个为主服务器
    'SESSION_REDIS_PORT'	=>  '6379',	       //端口,如果相同只填一个,用英文逗号分隔
    'SESSION_REDIS_AUTH'    =>  '',     //Redis auth认证(密钥中不能有逗号),如果相同只填一个,用英文逗号分隔

    'HTML_CACHE_ON' => false, // 开启静态缓存true 开启
    'HTML_CACHE_TIME' => 60,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX' => '.shtml', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES' => array(
        'Index:' => array('{:controller}_{:action}_{id}'),
    ),
    //开启redis
    'DATA_CACHE_TYPE'=>'Redis',
    'REDIS_HOST'=>'127.0.0.1',
    'REDIS_PORT'=>6379,
    //购物车cookie
    'SHOPPING_CAR_COOKIE_KEY'=>'USER_SHOPPING_CAR',
);