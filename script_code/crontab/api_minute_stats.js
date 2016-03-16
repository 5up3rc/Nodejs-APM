/**
 * @description 格式化日期为指定的格式
 * @param {String} pattern 输出格式, %Y/%M/%d/%h/%m/%s %w的组合
 * @param {Boolean} [isFill:false] 不足两位是否补0
 * @return {String}
 * @example
 var t=new Date();
 t.format('%Y/%M/%d %h:%m:%s');
 输出：2012/07/16 16:05:30
 */
Date.prototype.format = function (pattern) {
    var Y = this.getFullYear();
    var M = this.getMonth() + 1;
    var d = this.getDate();
    var h = this.getHours();
    var m = this.getMinutes();
    var s = this.getSeconds();
    var w = this.getDay();
    var week = ['日', '一', '二', '三', '四', '五', '六'];
    w = week[w];
    M = (M < 10) ? ('0' + M) : M;
    d = (d < 10) ? ('0' + d) : d;
    h = (h < 10) ? ('0' + h) : h;
    m = (m < 10) ? ('0' + m) : m;
    s = (s < 10) ? ('0' + s) : s;
    w = w;
    pattern = pattern || '%Y-%M-%d %h:%m:%s';
    pattern = pattern.replace('%Y', Y);
    pattern = pattern.replace('%M', M);
    pattern = pattern.replace('%d', d);
    pattern = pattern.replace('%h', h);
    pattern = pattern.replace('%m', m);
    pattern = pattern.replace('%s', s);
    pattern = pattern.replace('%w', w);
    return pattern;
};

// Infobright服务器连接配置
var ib_config = {
	host : "172.24.180.196", 
	user : "root",			
	port : 5029,
	database : "BH_log_center",
	password : '123',
};

// load mysql module
var mysql = require('mysql');
// 取当前时间缀
var time = new Date().getTime();
var p_start_date = new Date(time - 600000).format("%Y-%M-%d %h:%m:00"); // 取10分钟之前整分钟的时间点
var p_start_time = parseInt(new Date(p_start_date).getTime()/1000);
var p_end_date = new Date(time - 540000).format("%Y-%M-%d %h:%m:00"); // 取9分钟之前整分钟的时间点
var p_end_time = parseInt(new Date(p_end_date).getTime()/1000);
var p_date = p_start_date.substring(0, 10);
// 创建一个连接
var connection = mysql.createConnection(ib_config);

// 检查连接是否成功
connection.connect(function(err) {
	if (err) {
		console.log("[query] - : ", err);
	} else {
		console.log("[connection connect] - : succeed!");	
	}
});

// 删除重复数据
var delete_sql = 'delete from api_minute_stats where request_date="'+p_date+'" and request_minute = ' + p_start_time;
connection.query(delete_sql, function (err, result) {
	if (err) {
		console.log("[DELETE ERROR] - ", err.message);
	} else {
		console.log("[DELETE SUCCEED] -", delete_sql);
	}
});

// 插入新数据
var insert_sql = 'insert into api_minute_stats(cmd,request_date,request_minute,request_count,created,project_type,exectime,request_min_time,request_max_time) select cmd, "'+p_date+'", '+p_start_time+', count(cmd),unix_timestamp(now()),project_type,sum(exectime),min(exectime),max(exectime) from BH_t_linelog where request_time >= '+p_start_time+' and request_time < '+p_end_time+' and log_type="Access" group by cmd;';
connection.query(insert_sql, function (err, result) {
	if (err) {
		console.log("[insert error] - ", err.message);
	} else {
		console.log("insert succeed : ", insert_sql);	
	}
});

connection.end(function(err){
	if (err) {
		console.log("[query end] - : ", err);
	}
});