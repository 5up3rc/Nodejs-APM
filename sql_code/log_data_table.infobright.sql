create database  if not exists BH_log_center;
use BH_log_center;

CREATE table if not exists `BH_t_linelog` (
  `log_type` varchar(50) DEFAULT NULL,
  `hostip` varchar(100) DEFAULT NULL COMMENT 'lookup',
  `playerid` int(11) DEFAULT '-1',
  `module` varchar(100) DEFAULT NULL,
  `cmd` varchar(200) DEFAULT NULL COMMENT 'lookup',
  `errcode` int(11) DEFAULT NULL,
  `retcode` int(11) DEFAULT '-1',
  `project_type` int(11) DEFAULT '1',
  `srcfile` varchar(200) DEFAULT NULL,
  `srcline` int(11) DEFAULT NULL,
  `func` varchar(200) DEFAULT NULL COMMENT 'lookup',
  `pid` int(11) DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `msg` varchar(400) DEFAULT NULL,
  `exectime` int(11) DEFAULT NULL,
  `useagent` varchar(1000) DEFAULT NULL,
  `request_time` int(11) DEFAULT NULL
) ENGINE=BRIGHTHOUSE DEFAULT CHARSET=utf8;


CREATE TABLE `api_minute_stats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_type` int(11) NOT NULL DEFAULT '0',
  `cmd` varchar(100) NOT NULL,
  `request_date` date NOT NULL,
  `request_minute` int(11) DEFAULT NULL,
  `request_count` int(11) NOT NULL DEFAULT '0',
  `request_min_time` int(11) NOT NULL DEFAULT '0',
  `request_max_time` int(11) NOT NULL DEFAULT '0',
  `exectime` int(11) DEFAULT '0',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`,`created`),
  UNIQUE KEY `request_date` (`request_date`,`request_minute`,`cmd`),
  KEY `cmd` (`cmd`,`request_date`,`request_minute`,`request_count`)
) ENGINE=MyISAM AUTO_INCREMENT=329 DEFAULT CHARSET=utf8 COMMENT='api_minute_stats';


CREATE TABLE `BH_server_behavior_stats` (
  `hostip` varchar(100) DEFAULT NULL COMMENT 'lookup',
  `project_type` int(11) DEFAULT '1',
  `identify` varchar(200) DEFAULT NULL ,
  `counts` int(11) DEFAULT '0' ,
  `report_time` int(11) DEFAULT '0' ,
  `min_exectime` decimal(10,4) DEFAULT '0.0000' ,
  `max_exectime` decimal(10,4) DEFAULT '0.0000' ,
  `avg_exectime` decimal(10,4) DEFAULT '0.0000' ,
  `mem` int(11) DEFAULT '0' ,
  `cpu` int(11) DEFAULT '0' ,
  `request_time` int(11) DEFAULT '0' 
) ENGINE=BRIGHTHOUSE DEFAULT CHARSET=utf8;