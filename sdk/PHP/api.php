<?php
// 系统执行最初始时间
$init_time = microtime ( TRUE );
include "ReportData.php";
$conf = array(
	'host' => '127.0.0.1', // 上报日志服务器IP（一般是本机或同内网机) default 127.0.0.1
    'port' => 6969,  // 上报日志服务器Socket端口号 default 6969
    'ratio' => 100,     //上报日志的比例 default 100
    'is_need_send' => TRUE, //是否开启上报 default false
    'api_init_time' => microtime ( TRUE ), //API系统的初始化时间(一般是在执行index.php的第一行获取)
    'project_type' => 1, //上报项目编号 default 1
    'max_msg_len' => 500, //MSG内容最大长度 default 500
);

$playerid = 1;
$module = 'test_module'; // test module
$cmd = '/api.php'; // api name

$ReportData = new ReportData($conf);

// point one
$ReportData->set_exe_log_point("start_sys");

testfunc();
// point twe
$ReportData->set_exe_log_point("run func-testfunc");
try {
	testfunc1();
} catch ( Exception $e ) {
	$ReportData->send_error_log($module, $playerid, $cmd, 1, $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
}
// point three
$ReportData->set_exe_log_point("run func-testfunc1");
$ReportData->send_access_log($playerid, $module, $cmd);

function testfunc(){
    $a = 'test';
    $b = 'test111';
    return $a;
}

function testfunc1(){
    $t = 222111;
    $m = 222;
    return $t-$m;
}