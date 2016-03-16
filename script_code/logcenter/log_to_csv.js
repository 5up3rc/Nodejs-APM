/**
 * Intril.leng <jj.comeback@gmail.com>
 * Created on 2016/3/11.
 */
var Config = {
    port: 6969,
    exportFlag: false,
    logPath: '/usr/local/logcenter',
    buffer: []
};

var ExBuffer = require('ExBuffer');
var net = require('net');
var fs = require('fs');

// evey one minute run times
var interval = setInterval(function () {
    // init data
    Config.exportFlag = true;
    var dataStr = '';
    dataStr = Config.buffer.join('');
    Config.buffer.length = 0;

    var now = new Date();
    var fileName = now.format("%Y%M%d%h%m");
    var folder = now.format("%Y%M%d");
    if (!fs.existsSync(Config.logPath)) {
        fs.mkdirSync(Config.logPath);
    }
    if (!fs.existsSync(Config.logPath + "/" + folder)) {
        fs.mkdirSync(Config.logPath + "/" + folder);
    }
    fs.open(Config.logPath + "/" + folder + "/" + fileName + ".csv", "a", 0644, function (e, fd) {
        if (e) throw e;
        fs.write(fd, dataStr, 0, 'utf8', function (e) {
            if (e) throw e;
            fs.closeSync(fd);
            Config.exportFlag = false;
        })
    });
}, 60000);

// create log center process
net.createServer(function (sock) {
    var exBuffer = new ExBuffer();
    // on received proxy data
    exBuffer.on('data', function (bytes) {
        var dataStr = bytes.toString();
        if (Config.exportFlag == false) {
            if (dataStr != "" && dataStr != "\n") {
                // package 1 minute data in memory
                Config.buffer.push(dataStr + "\n");
            }
        }
    });

    sock.on('data', function (data) {
        exBuffer.put(data);
    });

}).listen(Config.port);
console.log('Start - log_to_csv.js server accepting connection on port: ' + Config.port);

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