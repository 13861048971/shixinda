<?php
return array(
	//关闭session 自动启动
	'SESSION_AUTO_START' =>true,
	//'配置项'=>'配置值'
	'URL_MODEL' 		=>  2,
	'SHOW_PAGE_TRACE'	=> false,
	'MODULE_ALLOW_LIST' => array('Home','Admin','Cli'),
	'DEFAULT_MODULE'	=> 'Home',
	'DEFAULT_CHARSET'	=> 'UTF-8',
	'URL_HTML_SUFFIX'	=> '',
	
	//自动加载
	'APP_USE_NAMESPACE' => false,
	'APP_AUTOLOAD_PATH' => 'Common.Model,Common.Widget',
	
	'DATA_CACHE_SUBDIR'     =>  true,    // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    'DATA_PATH_LEVEL'       =>  1, 
	
	//数据库配置
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => 'localhost', // 服务器地址
	'DB_NAME'   => 'shixinda', // 数据库名
	'DB_USER'   => 'root', // 用户名
	'DB_PWD'    => '1234', // 密码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => '',
    
    //错误
    'LOG_LEVEL' =>'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误
    
    //七牛云
    'QINIUYUN' => array (
        'secrectKey' => 'Wpf7-OJZL-S93oGYcWCpXL88N0T7nikWVk9LepmB',
        'accessKey' => 'h7hX1-X0WlqlIxILxl09xaiKmc4EqZ6NH1lkfbte',
        'domain' => 'http://qiniu.img.mallshangyun.com/',
        'bucket' => 'shixinda',
        'imgStyle' => [
            '-250_250_thumb'
        ],
        'imgName' => [
            'userAvatar' => 'userAvatar', //用户头像
            'contentCover' => 'contentCover', //内容封面
            'navigationLogo' => 'navigationLogo', //导航logo
            'friendLinkLogo' => 'friendLinkLogo', //友情链接logo
            'blockImage' => 'blockImage' ,//轮播图
        ]
    )
            
   
);