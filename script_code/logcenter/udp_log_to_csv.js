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

var dgram = require("dgram");
var fs = require('fs');

//60s update to hd
var interval = setInterval(function () {
    // init data
    Config.exportFlag = true;
    var dataStr = '';
    dataStr = Config.buffer.join('');
    Config.buffer.length = 0;

    var now = new Date();
    var fileName = "udp_" + now.format("%Y%M%d%h%m");
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

//udp server
var server = dgram.createSocket("udp4");
server.on("error", function (err) {
    console.log("udp server error:\n" + err.stack);
    server.close();
});

server.on("message", function (message) {
    // add buffer
    var dataStr = message.toString();
    dataArr = dataStr.split(",");
    if (dataArr.length != 11) {
        console.log("error message: " + dataStr);
        Config.exportFlag = true;
    }
    if (Config.exportFlag == false) {
        if (dataStr != "" && dataStr != "\n") {
            Config.buffer.push(dataStr + "\n");
        }
    }
});

server.on("listening", function () {
    var address = server.address();
    console.log("server listening " + address.address + ":" + address.port);
});

server.bind(Config.port);