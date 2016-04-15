#!/usr/bin/env python
#_*_ coding:utf-8 _*_

import sys 
reload(sys) 
sys.setdefaultencoding('utf-8') 
import os
import time, datetime

# load sdk
import ReportSDK from ReportSDK

# config 
config = {
	"host" : "127.0.0.1", 	# report proxy server ip default 127.0.0.1
	"port" : 6969,			# report proxy server socket port default 6969
	"ratio" : 100,			# report log rate default 100
	"is_need_send" : False, # is open report default False
	"api_init_time" : 0,    # program init run time default 0
	"project_type" : 1,     # report project id default 1
	"max_msg_len" : 500		# report block msg length default 500
}

playerid = 1
module = 'test_module'
cmd = '/example.py'
config.api_init_time = time.time() * 1000
ReportSDK = ReportSDK( config )

# point one
ReportSDK.set_exe_log_point("start_sys")

testfunc();
# point twe
ReportSDK.set_exe_log_point("run func-testfunc");

try:
	testfunc()
except Exception, e:
	ReportSDK.send_error_log( module, playerid, cmd, 1, e.getCode, e.getMessage, e.getFile, e.getLine )
# point three
ReportSDK.set_exe_log_point("run func-testfunc1");
ReportSDK.send_access_log( module, playerid, cmd );

def testfunc():
	time.sleep(0.1);
	print "Test Run testfunc"


def testfunc1():
	time.sleep(1);
	print "Test Run testfunc1"
