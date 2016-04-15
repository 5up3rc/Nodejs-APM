<?php
// 系统执行最初始时间
$init_time = microtime ( TRUE );
include "ReportSDK.php";
$conf = array(
	'host' => '127.0.0.1',      # report proxy server ip default 127.0.0.1
    'port' => 6969,             # report proxy server socket port default 6969
    'ratio' => 100,             # report log rate default 100
    'is_need_send' => TRUE,     # is open report default False
    'api_init_time' => microtime ( TRUE ), # program init run time default 0
    'project_type' => 1,        # report project id default 1
    'max_msg_len' => 500,       # report block msg length default 500
);

$playerid = 1;
$module = 'test_module'; // test module
$cmd = '/example.php'; // api name

$ReportSDK = new ReportSDK($conf);

// point one
$ReportSDK->set_exe_log_point("start_sys");

testfunc();
// point twe
$ReportSDK->set_exe_log_point("run func-testfunc");
try {
	testfunc1();
} catch ( Exception $e ) {
	$ReportSDK->send_error_log($module, $playerid, $cmd, 1, $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
}
// point three
$ReportSDK->set_exe_log_point("run func-testfunc1");
$ReportSDK->send_access_log($playerid, $module, $cmd);

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