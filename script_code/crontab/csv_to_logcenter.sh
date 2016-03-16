#!/bin/bash

#infobright配置
MYSQL_DB_HOST_BH='10.50.2.201' #'localhost'
MYSQL_DB_USER_BH='dgc_info'
MYSQL_DB_PORT_BH='5029'
MYSQL_DB_NAME_BH='BH_log_center'
MYSQL_DB_PASS_BH='dgcinfo#2012'
mysql_login_BH="mysql -h$MYSQL_DB_HOST_BH -P$MYSQL_DB_PORT_BH -u$MYSQL_DB_USER_BH -p$MYSQL_DB_PASS_BH $MYSQL_DB_NAME_BH" #连接数据库

#临时文件路径
CSV_PATH="/usr/local/logcenter"
# if not exists data directory then creat it
if [ ! -d $CSV_PATH ];then
  mkdir  $CSV_PATH
fi

#just load data at one minute ago
load_data_name=`date -d '-1 minutes ' "+%Y%m%d%H%M"`
echo ${load_data_name}
#find log data directory
directory=`date -d '-1 minutes ' "+%Y%m%d"`
echo ${directory}
total_end_date=`date '+%Y-%m-%d %H:%M:%S'`
CSV_PATH_FILE="${CSV_PATH}/${directory}/${load_data_name}.csv"
echo ${CSV_PATH_FILE}

# 内网服务存日志位置
#DST_CSV_PATH_FILE=/tmp/${load_data_name}.csv 
#if [ -s "$DST_CSV_PATH_FILE" ] ;then
	#去掉空行
	
	#from nodejs logcenter to mysql center. 192.168.2.3 is logcenter ip
	#scp 10.50.2.91:${CSV_PATH_FILE} $DST_CSV_PATH_FILE
	sed -i '/^$/d' $CSV_PATH_FILE
	#from mysql center to infobright. 192.168.2.3 is infobright ip
	#scp $DST_CSV_PATH_FILE 10.50.2.201:$DST_CSV_PATH_FILE
	
if [ -s "$CSV_PATH_FILE" ] ;then
  # load data to infobright
	`${mysql_login_BH} -e "LOAD DATA INFILE '$CSV_PATH_FILE' INTO TABLE BH_t_linelog  FIELDS TERMINATED BY ',' ENCLOSED BY '' LINES TERMINATED BY '\n' STARTING BY '' "` 
fi
