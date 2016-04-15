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
 * $project Int 1 SNS-APP.
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

class ReportSDK {
    /**
     * default configure
     * @var array
     */
    private $_conf = array(
        'host' => '127.0.0.1',      // report proxy server ip default 127.0.0.1
        'port' => 6969,             // report proxy server socket port default 6969
        'ratio' => 100,             // report log rate default 100
        'is_need_send' => false,    // is open report default False
        'api_init_time' => 0,       // program init run time default 0
        'project_type' => 1,        // report project id default 1
        'max_msg_len' => 500,       // report block msg length default 500
    );

    /**
     * last set point time
     * @var number
     */
    public $last_point_time;

    /**
     * point execute log
     * @var array
     */
    public $exec_log_point = array();

    /**
     * init report config
     */
    public function __construct ( $conf = null ) {
        if (intval($conf['ratio']) > 100){
            $conf['ratio'] = 100;
        }
        $this->_conf = array_merge($this->_conf, $conf);
        // init last point time
        $this->last_point_time = $this->_conf['api_init_time'];
    }

    /**
     * set code block point
     * @param $p
     */
    public function set_exe_log_point ( $p ) {
        $end_time = microtime ( true );
        $exec_time = round ( ( $end_time - $this->last_point_time ) * 1000, 1 );
        $this->last_point_time = $end_time;
        $this->exec_log_point[$p] = $exec_time;
    }

    /**
     * API Access Log
     * @param        $playerid  user id default 0
     * @param        $module    module name like order
     * @param        $cmd       api name
     * @param string $msg       access point msg
     * @return bool
     */
    public function send_access_log ( $playerid, $module, $cmd, $msg = '' ) {
        if ( $this->_conf['is_need_send'] == false ) { // is need to send report
            return false;
        }
        // rand number to check report this times or not
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
     * Send Error Log Data API  level: LOG_DEBUG, LOG_INFO, LOG_WARN, LOG_ERROR, LOG_FATAL
     * @param        $module    module name like order
     * @param        $playerid  user id default 0
     * @param        $cmd       api name
     * @param        $level     error report level 1,2,3,4,5
     * @param        $errcode   error code
     * @param        $msg       error msg like mysql has gone away
     * @param string $file_name error in code file
     * @param string $file_line error in code line
     * @return bool
     */
    public function send_error_log ( $module, $playerid, $cmd, $level, $errcode, $msg, $file_name = '', $file_line = '' ) {
        if ( $this->_conf['is_need_send'] == false ) { // is need to send report
            return false;
        }
        // nothing is ""
        $useagent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
        // execute time default 0
        $exectime = 0;
        @$this->error_log ( $module, $playerid, $cmd, $level, $errcode, $msg, $this->_conf['project_type'], $exectime, $useagent, $file_name, $file_line );
    }

    /**
     * send error log data
     *
     * @param      $module      module name like order
     * @param      $playerid    user id default 0
     * @param      $cmd         api name
     * @param      $level       error report level 1,2,3,4,5
     * @param      $errcode     error code
     * @param      $msg         error msg like mysql has gone away
     * @param      $project     project id Administartor to give
     * @param      $exectime    execute time default 0
     * @param      $useagent    useagent nothing is ""
     * @param null $file        error in code file
     * @param null $line        error in code line
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
     * send access log data
     *
     * @param $playerid     user id default 0
     * @param $module       module name like order
     * @param $cmd          api name
     * @param $retcode      error code default 200
     * @param $msg          access point msg default json
     * @param $project      project id Administartor to give
     * @param $exectime     execute time like 30 (ms)
     * @param $useagent     useagent nothing is ""
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
     * the same to printf use like this logprintf(format, data1, data2, ....);
     * less more than 8 args, the first is fomart
     *
     * @return int 0:success -1:fail
     */
    private function send_socket () {
        $num = func_num_args ();
        // 7 must args and 1 fomart args
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