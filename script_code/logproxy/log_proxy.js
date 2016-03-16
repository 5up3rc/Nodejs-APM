/**
 * Intril.leng <jj.comeback@gmail.com>
 * Created on 2016/3/11.
 */
var Config = {
    logProxyPort: 6969,
    logToCsv: {host: '172.24.180.196', port: 6969},
    connectLogToCsv: true,
    connectTimes: 1
};

// load module
var net = require('net');
var fs = require('fs');

// create socket object
var servicesSocket = new net.Socket();

// evey 30 second to connect log_to_csv.js
var interval = setInterval(function () {
    var now = new Date();
    // connect log_to_csv.js socket services
    servicesSocket.connect(parseInt(Config.logToCsv.port), Config.logToCsv.host, function () {
        Config.connectLogToCsv = true;
        Config.connectTimes = 1;
        console.log(now.format(), 'connect to log_to_csv.js ok');
        clearTimeout(interval);
    });
    // when error
    servicesSocket.on('error', function () {
        Config.connectLogToCsv = false;
        console.log(now.format(), 'reconnect log_to_csv.js ' + Config.connectTimes + ' times');
        Config.connectTimes++;
        // reconnect 120 times, still connect error, exit
        if (Config.connectTimes > 120) {
            console.log(now.format(), 'connect log_to_csv.js fail');
            Config.connectTimes = 1;
            process.exit(0);
        }
    });
}, 30000);

// create log_proxy.js socket server
net.createServer(function (socket) {
    socket.on('data', function (binaryData) {
        // check connect to log_to_csv.js is ok
        if (Config.connectLogToCsv == true) {
            // add the packet header ( has data length info) to unpack on log_to_csv.js
            var dataLen = Buffer.byteLength(binaryData.toString());
            var headBuffer = new Buffer(2);
            headBuffer.writeUInt16BE(dataLen, 0);
            servicesSocket.write(headBuffer);
            var bodyBuffer = new Buffer(dataLen);
            bodyBuffer.write(binaryData.toString());
            servicesSocket.write(bodyBuffer);
        }
    });
}).listen(Config.logProxyPort);
console.log('Start - log_proxy.js server accepting connection on port: ' + Config.logProxyPort);

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