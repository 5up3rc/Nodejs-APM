#!/usr/bin/env python
#_*_ coding:utf-8 _*_

import sys 
reload(sys) 
sys.setdefaultencoding('utf-8') 
import os
import time, datetime
import json

"""
	Log Report SDK
	@desc report program run times, Error Info and block run time

	@demo usage for log_printf
	project intval 1 社交APP....
	ip = "192.168.1.1";
	playerid = 12345;
	biz = "finance.stock.dpfx";
	op = "login";
	status = 0;
	logid = 119;
	flowid = 345678;
	custom = "custom message from python";
	init("127.0.0.1", 6578);
	if(log_printf("%s,%d,%s,%s,%d,%d,%d,%s", ip, playerid, biz, op, status, logid, flowid, custom) < 0) {
		print "logprintf failed\n";
	}
"""
class ReportSDK ():

	"""
		default configure
	"""
	__conf = {
		"host" : "127.0.0.1", 	# report proxy server ip default 127.0.0.1
		"port" : 6969,			# report proxy server socket port default 6969
		"ratio" : 100,			# report log rate default 100
		"is_need_send" : False, # is open report default False
		"api_init_time" : 0,    # program init run time default 0
		"project_type" : 1,     # report project id default 1
		"max_msg_len" : 500		# report block msg length default 500
	}

	"""
		last set point time
	"""
	__last_point_time = 0

	"""
		exec log point
	"""
	__exec_log_point = {}

	def __init__( self, conf = None ):
		if conf.ratio > 100:
			conf.ratio = 100

        self.config = dict( self.config, **conf )
        # init last point time
        self.last_point_time = self.config.api_init_time

    """
    	to execute the code fragment
    """
    def set_exe_log_point ( self, point ):
    	end_time = time.time()*1000
    	exec_time = round( end_time - self.__last_point_time )
    	self.__last_point_time = end_time
    	self.__exec_log_point[point] = exec_time


    """
		API Access Log
		@param        $playerid  user id default 0
		@param        $module    module name like order
		@param        $cmd       api name
		@param string $msg       access point msg
		@return bool
    """
    def send_access_log( self, playerid, module, cmd, msg = "" ):
    	# is need to send report
    	if self.__conf["is_need_send"] is False:
    		return False
    	# rand number to check report this times or not
    	radio = random.randint(1, 100)
    	if radio > self.__conf["ratio"] :
    		return False
    	start_time = self.__conf["api_init_time"]

    	if msg is None:
    		msg = json.dumps(self.__exec_log_point)
    	exectime = round( end_time - self.__last_point_time )
    	useagent = ""
    	try:
    		self.access_log(playerid, module, cmd, 200, msg, self.__conf["project_type"], exectime, useagent )
    	except Exception, e:
    		return False

    """
        Send Error Log Data API  level: LOG_DEBUG, LOG_INFO, LOG_WARN, LOG_ERROR, LOG_FATAL
     	@param        $module    module name like order
     	@param        $playerid  user id default 0
     	@param        $cmd       api name
     	@param        $level     error report level 1,2,3,4,5
     	@param        $errcode   error code
     	@param        $msg       error msg like mysql has gone away
     	@param string $file_name error in code file
     	@param string $file_line error in code line
     	@return bool
    """
    def send_error_log (self, module, playerid, cmd, level, errcode, msg, file_name = "", file_line = "" ):
    	# is need to send report
    	if self.__conf["is_need_send"] is False:
    		return False
    	# nothing is ""
    	useagent = ""
    	# execute time default 0
    	$exectime = 0
    	try:
    		self.error_log( module, playerid, cmd, level, errcode, msg, self.__conf["project_type"], exectime, useagent, file_name, file_line )
    	except Exception, e:
    		return False


    """
    	the same to printf use like this logprintf(format, data1, data2, ....);
    	less more than 8 args, the first is fomart
    	@return int 0:success -1:fail
    """
    def send_socket( self );


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
   
   