/**
 * 日志上报服务
 * @desc 记录API的访问量，并且上报代码中的Error和代码片段的执行时间
 *       if (!defined('LOG_WARNING')) define('LOG_WARNING', 3);
 *       if (!defined('LOG_NOTICE')) define('LOG_NOTICE', 4);
 *       if (!defined('LOG_DEBUG')) define('LOG_DEBUG', 5);
 *       if (!defined('LOG_INFO')) define('LOG_INFO', 6);
 *       define('LOG_ERROR', 2);
 *       error_reporting(0);
 */
/**
 * demo usage for log_printf
 * $project 整形 1 社交APP.
 *
 * $ip = "192.168.1.1";
 * $playerid = 12345;
 * $biz = "finance.stock.dpfx";
 * $op = "login";
 * $status = 0;
 * $logid = 119;
 * $flowid = 345678;
 * $custom = "custom message from php";
 * init("127.0.0.1", 6578);
 * if(log_printf("%s,%d,%s,%s,%d,%d,%d,%s", $ip, $playerid, $biz, $op, $status, $logid, $flowid, $custom) < 0) {
 *      echo "logprintf failed\n";
 * }
 */

var ReportData = {

	/**
     * 上报日志服务器IP（一般是本机或同内网机) default 127.0.0.1
     * @var string
     */
    var _agent_ip;

    /**
     * 上报日志服务器Socket端口号 default 6969
     * @var string
     */
    var _agent_port;

    /**
     * 上报日志的比例 default 10
     * @var int
     */
    var _agent_ratio;

    /**
     * 是否开启上报 default false
     * @var bool
     */
    var _is_need_send;

    /**
     * 上报项目编号 default 1
     * @var int
     */
    var _project_type;

    /**
     * API请求的初始时间
     * @var int
     */
    var _init_time;

    /**
     * MSG内容最大长度 default 500
     * @var int
     */
    var _max_msg_len;

	/**
     * 初始化上报配置信息
     *
     * @param     $ip 上报服务器IP 通常就是本机ip
     * @param     $port
     * @param int $ratio 控制记录日志流水的比例,10表示存10%
     */
	init : function(){
		this._agent_ratio = 100;
		this._agent_ip = "172.24.180.96";
		this._agent_port = 6969;
		this._is_need_send = true;
		this._project_type = 1;
		this._max_msg_len = 500;
		this._init_time = new Date().getTime();
	},

	/**
     * 代码打点
     * @param $p
     */
	set_log_point: function ( point ){
		var set_exe_log_point = [];
		if ( set_log_point_time == undefined ) {
			var start_time = this._init_time; // 系统执行的初始时间
		} else {
			var start_time = set_log_point_time;
		}
		var end_time = new Date().getTime();
		var exec_time = round( end_time - start_time)
		set_log_point_time = exec_time;
        set_exe_log_point.push({point : exec_time});
	},

	/**
     * 访问流水日志API
     * @param        $playerid  玩家ID,无则0
     * @param        $module    模块名,比如订单处理等
     * @param        $oper      方法名称
     * @param string $msg       错误消息 access消息
     * @return bool
     */
	send_access_log: function(playerid, module, oper, msg){
		this.init();
		if (this._is_need_send == false) { // 如果没开启上报则直接返回
			return false;
		}
		// 获取随机数 如果不在存取范围内,则丢弃。默认只保存10分之一的数据.
		ratio = parseInt(Math.random() * 100);
		if (ratio > this._agent_ratio) {
			return;
		}
		$start_time = this._init_time;
        if ( empty( $msg ) ) {
            $set_exe_log_point = Cool::$GC['set_exe_log_point'];
            $msg = is_array ( $set_exe_log_point ) ? json_encode ( $set_exe_log_point ) : '';
        }
        $exectime = (int)( ( microtime ( true ) - $start_time ) * 1000 );
        $useagent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
        try {
            self::access_log ( $playerid, $module, $oper, 200, $msg, self::$_project_type, $exectime, $useagent );
        } catch ( Exception $e ) {

        }
	},

	/**
     * 发送错误日志API  level: LOG_DEBUG, LOG_INFO, LOG_WARN, LOG_ERROR, LOG_FATAL
     * @param        $module    模块名,比如订单处理等
     * @param        $playerid  玩家ID,无则0
     * @param        $cmd       命名名称
     * @param        $level     错误级别 1,2,3,4,5
     * @param        $errcode   错误码比如404
     * @param        $msg       错误消息 比如mysql has gone away
     * @param string $file_name 错误文件
     * @param string $file_line 错误行号
     * @return bool
     */
	send_error_log: function (){

	}

	/**
     * 发送错误日志
     *
     * @param      $module      模块名,比如订单处理等
     * @param      $playerid    玩家ID,无则0
     * @param      $cmd         命名名称
     * @param      $level       错误级别 1,2,3,4,5
     * @param      $errcode     错误码比如404
     * @param      $msg         错误消息 比如mysql has gone away
     * @param      $project     项目类型
     * @param      $exectime    执行时间,单位毫秒 比如30
     * @param      $useagent    useagent 无则为""
     * @param null $file        错误文件
     * @param null $line        错误行号
     * @return int
     */
     error_log: function(){

     },

     /**
     * 发送流水日志
     *
     * @param $playerid     玩家ID,无则0
     * @param $module       模块名,比如订单处理等
     * @param $oper         方法名称
     * @param $retcode      错误码 默认200
     * @param $msg          access消息
     * @param $project      项目类型
     * @param $exectime     执行时间,单位毫秒 比如30
     * @param $useagent     useagent 无则为""
     * @return int
     */
     access_log: function (){

     },

     /**
     * 与printf的用法相似 logprintf(format, data1, data2, ....);
     * 至少要包含8个参数，其中第一个为格式串，其余为对应的数据内容(至少7个必填字段。请参考接口文档说明)
     *
     * @return int 0:成功 -1:失败
     */
     log_printf : function(){
        var socket = new socket
     }
}
exports.ReportData = ReportData;
