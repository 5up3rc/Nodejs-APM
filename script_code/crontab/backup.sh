#!/bin/bash
# Author:jj.comeback@gmail.com

# nodelogcenter接收存放日志位置
log_path=/usr/local/logcenter/
# 日志压缩后存放的位置
backup_path=/usr/local/backup/
cd $log_path
today=`date +%Y%m%d`
for dir in `ls $log_path --file-type -1`
do
    backup_name=`echo ${dir:0:8}`
    if [ "$today" -ne "$backup_name" ];then
        tar -zcf $backup_path$backup_name\.tar.gz $dir;
        rm -rf $log_path$dir
    fi
done
