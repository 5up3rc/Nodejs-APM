<?php
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

class ReportData {
    /**
     * default configure
     * @var array
     */
    private $_conf = array(
        'host' => '127.0.0.1', // 上报日志服务器IP（一般是本机或同内网机) default 127.0.0.1
        'port' => 6969,  // 上报日志服务器Socket端口号 default 6969
        'ratio' => 100,     //上报日志的比例 default 100
        'is_need_send' => false, //是否开启上报 default false
        'api_init_time' => 0, //API系统的初始化时间(一般是在执行index.php的第一行获取)
        'project_type' => 1, //上报项目编号 default 1
        'max_msg_len' => 500, //MSG内容最大长度 default 500
    );

    /**
     * 上一次打点时的时间
     * @var number
     */
    public $last_point_time;

    /**
     * 打点记录
     * @var array
     */
    public $exec_log_point = array();

    /**
     * 初始化上报配置信息
     */
    public function __construct ( $conf = null ) {
        if (intval($conf['ratio']) > 100){
            $conf['ratio'] = 100;
        }
        $this->_conf = array_merge($this->_conf, $conf);
        // 初始化时 上一次打点时间
        $this->last_point_time = $this->_conf['api_init_time'];
    }

    /**
     * 代码打点
     * @param $p
     */
    public function set_exe_log_point ( $p ) {
        $end_time = microtime ( true );
        $exec_time = round ( ( $end_time - $this->last_point_time ) * 1000, 1 );
        $this->last_point_time = $end_time;
        $this->exec_log_point[$p] = $exec_time;
    }

    /**
     * 访问流水日志API
     * @param        $playerid  玩家ID,无则0
     * @param        $module    模块名,比如订单处理等
     * @param        $cmd      方法名称
     * @param string $msg       错误消息 access消息
     * @return bool
     */
    public function send_access_log ( $playerid, $module, $cmd, $msg = '' ) {
        if ( $this->_conf['is_need_send'] == false ) { // 如果没开启上报则直接返回
            return false;
        }
        // 获取随机数 如果不在存取范围内,则丢弃。默认只保存10分之一的数据.
        $radio = rand ( 1, 100 );
        if ( $radio > $this->_conf['ratio'] ) {
            return;
        }
        $start_time = $this->_conf['api_init_time'];
        if ( empty( $msg ) ) {
            $msg = is_array ( $this->exec_log_point ) ? json_encode ( $this->exec_log_point ) : '';
        }
        $exectime = (int)( ( microtime ( true ) - $start_time ) * 1000 );
        $useagent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
        @$this->access_log ( $playerid, $module, $cmd, 200, $msg, $this->_conf['project_type'], $exectime, $useagent );
    }

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
    public function send_error_log ( $module, $playerid, $cmd, $level, $errcode, $msg, $file_name = '', $file_line = '' ) {
        if ( $this->_conf['is_need_send'] == false ) { // 如果没开启上报则直接返回
            return false;
        }
        // 无则为""
        $useagent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
        // 执行时间,单位毫秒 比如30
        $exectime = 0;
        @$this->error_log ( $module, $playerid, $cmd, $level, $errcode, $msg, $this->_conf['project_type'], $exectime, $useagent, $file_name, $file_line );
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
    private function error_log ( $module, $playerid, $cmd, $level, $errcode, $msg, $project, $exectime, $useagent, $file = null, $line = null ) {
        if ( !isset( $module ) || !isset( $cmd ) || !isset( $level ) || !isset( $errcode ) || !isset( $msg ) || !isset( $project ) ) {
            return -1;
        }
        if ( !isset( $playerid ) ) $playerid = "";

        if ( !isset( $exectime ) ) $exectime = 0;

        if ( !isset( $useagent ) ) $useagent = "";

        $localip = $_SERVER['SERVER_ADDR'];
        if ( $localip == "" ) $localip = "unkown";

        if ( function_exists ( "posix_getpid" ) ) {
            $pid = posix_getpid ();
        } else {
            $pid = get_current_user ();
        }

        if ( !empty( $file ) && !empty( $line ) ) {
            $srcfile = $file;
            $func = '';
            $srcline = $line;
        } else {
            $e = new Exception( "" );
            $trace = $e->getTrace ();
            if ( isset( $trace[2] ) && isset( $trace[2]["file"] ) && isset( $trace[2]["line"] ) && isset( $trace[2]["function"] ) ) {
                $srcfile = $trace[2]["file"];
                $func = $trace[2]["function"];
                $srcline = $trace[1]["line"];
            } else if ( isset( $trace[1] ) && isset( $trace[1]["file"] ) && isset( $trace[1]["line"] ) && isset( $trace[1]["function"] ) ) {
                $srcfile = $trace[1]["file"];
                $func = $trace[1]["function"];
                $srcline = $trace[1]["line"];
            } else {
                $srcfile = $trace[0]["file"];
                $func = $trace[0]["function"];
                $srcline = $trace[0]["line"];
            }
        }

        if ( strlen ( $msg ) > $this->_conf['max_msg_len'] ) $msg = substr ( $msg, 0, $this->_conf['max_msg_len'] );

        $msg = str_replace ( "&", " ", $msg );
        $msg = str_replace ( ",", "&", $msg );
        $useagent = str_replace ( "&", " ", $useagent );
        $useagent = str_replace ( ",", "&", $useagent );
        $request_time = time ();

        return $this->send_socket ( "%s,%s,%u,%s,%s,%d,%d,%d,%s,%d,%s,%d,%d,%s,%d,%s,%d",
                                 'Error', $localip, $playerid, $module, $cmd, $errcode, 0, $project,
                                 $srcfile, $srcline, $func, $pid, $level, $msg, $exectime, $useagent, $request_time );
    }

    /**
     * 发送流水日志
     *
     * @param $playerid     玩家ID,无则0
     * @param $module       模块名,比如订单处理等
     * @param $cmd          方法名称
     * @param $retcode      错误码 默认200
     * @param $msg          access消息
     * @param $project      项目类型
     * @param $exectime     执行时间,单位毫秒 比如30
     * @param $useagent     useagent 无则为""
     * @return int
     */
    private function access_log ( $playerid, $module, $cmd, $retcode, $msg, $project, $exectime, $useagent ) {
        if ( !isset( $module ) || !isset( $playerid ) || !isset( $cmd ) || !isset( $retcode ) || !isset( $msg ) || !isset( $project ) ) {
            return -1;
        }
        if ( !isset( $playerid ) ) $playerid = "";

        if ( !isset( $exectime ) ) $exectime = 0;

        if ( !isset( $useagent ) ) $useagent = "";

        $localip = $_SERVER['SERVER_ADDR'];
        if ( $localip == "" ) $localip = "unkown";
        if ( function_exists ( "posix_getpid" ) ) {
            $pid = posix_getpid ();
        } else {
            $pid = get_current_user ();
        }

        $e = new Exception( "" );
        $trace = $e->getTrace ();
        if ( isset( $trace[2] ) && isset( $trace[2]["file"] ) && isset( $trace[2]["line"] ) && isset( $trace[2]["function"] ) ) {
            $srcfile = $trace[2]["file"];
            $func = $trace[2]["function"];
            $srcline = $trace[2]["line"];
        } else if ( isset( $trace[1] ) && isset( $trace[1]["file"] ) && isset( $trace[1]["line"] ) && isset( $trace[1]["function"] ) ) {
            $srcfile = $trace[1]["file"];
            $func = $trace[1]["function"];
            $srcline = $trace[1]["line"];
        } else {
            $srcfile = $trace[0]["file"];
            $func = $trace[0]["function"];
            $srcline = $trace[0]["line"];
        }

        if ( strlen ( $msg ) > $this->_conf['max_msg_len'] ) $msg = substr ( $msg, 0, $this->_conf['max_msg_len'] );

        $msg = str_replace ( "&", " ", $msg );
        $msg = str_replace ( ",", "&", $msg );
        $useagent = str_replace ( "&", " ", $useagent );
        $useagent = str_replace ( ",", "&", $useagent );

        $request_time = time ();
        return $this->send_socket ( "%s,%s,%u,%s,%s,%d,%d,%d,%s,%d,%s,%d,%d,%s,%d,%s,%d",
                                 'Access', $localip, $playerid, $module, $cmd, 0, $retcode, $project,
                                 $srcfile, $srcline, $func, $pid, 0, $msg, $exectime, $useagent, $request_time );
    }

    /**
     * 与printf的用法相似 logprintf(format, data1, data2, ....);
     * 至少要包含8个参数，其中第一个为格式串，其余为对应的数据内容(至少7个必填字段。请参考接口文档说明)
     *
     * @return int 0:成功 -1:失败
     */
    private function send_socket () {
        $num = func_num_args ();
        // 7个必填的字段，再加一个格式串
        if ( $num < 5 ) {
            return -1;
        }
        $args = func_get_args ();
        $log = vsprintf ( $args[0], array_slice ( $args, 1 ) );
        $socket = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP );
        if ( $socket < 0 ) {
            return -1;
        }
        if ( !@socket_connect ( $socket, $this->_conf['host'], $this->_conf['port'] ) ) {
            socket_close ( $socket );
            return -1;
        }
        $len = strlen ( $log );
        $ret = socket_write ( $socket, $log, strlen ( $log ) );
        if ( $ret != $len ) {
            socket_close ( $socket );
            return -1;
        }
        socket_close ( $socket );
        return 0;

    }
}