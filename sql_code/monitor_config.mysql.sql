create database if not exists monitor_config;
use monitor_config;
DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '项目ID',
  `name` varchar(255) NOT NULL COMMENT '项目名称',
  `ratio` int(11) NOT NULL DEFAULT '100' COMMENT '收集数据比例最大100',
  `dept` varchar(255) NOT NULL COMMENT '所属部门',
  `leader` varchar(255) NOT NULL COMMENT '负责人',
  `tel` varchar(255) NOT NULL COMMENT '负责人手机号',
  `desc` varchar(255) DEFAULT NULL COMMENT '描述',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

