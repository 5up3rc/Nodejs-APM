<?php

// 设置错误报告级别
error_reporting ( E_ALL | E_STRICT ); //

// session 配置
$config ['session'] = array( 'start' => true, 'namespace' => 'Default');
$config ['sub_folder'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', BASE_PATH));
$config ['site_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $config['sub_folder'];
$config ['base_path'] = BASE_PATH;
$config ['log_path'] = APP_PATH . 'cache/';
$config ['debug'] = true;
$config ['auto_route'] = true;

$config ['default_database'] = 'backend';
$config ['page_size'] = 10;
/* End of file config.php */