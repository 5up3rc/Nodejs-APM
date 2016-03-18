# Nodejs-APM

感谢Fred.Yu大神的授权，在大神原来系统之上进行了二次开发。并在此开源这套Nodejs+PHP完成的简单APM系统，欢迎大家多多提交其它语言的SDK。


目录结构
--

````
Nodejs-APM/
├── doc
│   └── install_document.docx
├── script_code
│   ├── crontab
│   │   ├── api_minute_stats.js
│   │   ├── backup.sh
│   │   ├── crontab_db.txt
│   │   └── csv_to_logcenter.sh
│   ├── logcenter
│   │   ├── log_to_csv.js
│   │   └── udp_log_to_csv.js
│   └── logproxy
│       ├── log_proxy.js
│       └── udp_log_proxy.js
├── sdk
│   ├── Nodejs
│   │   └── ReportData.js
│   └── PHP
│       ├── api.php
│       └── ReportData.php
├── source_code
└── sql_code
    ├── log_data_table.infobright.sql
    └── monitor_config.mysql.sql
````

部署过程
--

 - 将对应语言的SDK(PHP/JAVA/Python)放到需要监控的项目中加载，如PHP的：sdk/ReportData.php

 - 在Mysql服务器上面安装Mysql5.5以上版本

 - 执行sql_code/monitor_config.sql 文件，创建项目配置表

 - 在服务器上面安装Infobright服务（免费版即可）

 - 执行sql_code/log_data_table.infobright.sql 文件，创建infobright数据库

 - 在需要监控API的PHP/JAVA/Python等等Web机上面安装Nodejs，版本选择0.8.14上以，如安装node-v0.8.14.tar.gz


```
// Nodejs安装过程
tar zxvf node-v0. 8.14.tar.gz
cd node-v0.8.14
./configure --prefix=/usr/local/node
make
make install

// 安装ExBuffer库
cd  /usr/local/services/node-v0.8.14
npm install ExBuffer

//运行log_proxy.js脚本
node log_proxy.js >>/var/log/log_proxy.log &
```

 - 在收集log的服务器上面安装Nodejs(一般与WEB同一个机房走内网)，版本选择0.8.14上以，如安装node-v0.8.14.tar.gz

```
// Nodejs安装过程：
tar zxvf node-v0. 8.14.tar.gz
cd node-v0.8.14
./configure --prefix=/usr/local/node
make
make install

// 安装ExBuffer库
cd  /usr/local/services/node-v0.8.14
npm install ExBuffer

// 运行log_to_csv.js脚本
node log_to_csv.js >>/var/log/log_to_csv.log &
```

 - 启动Crontab进程

```
#exec load log center every minute
*/1 * * * *  /crontab/csv_to_logcenter.sh >>/var/logs/csv_to_logcenter.sh.log 2>&1 &
*/1 * * * *  node /crontab/api_minute_stats.js >>/var/logs/api_minute_stats.js.log 2>&1 &

# every day exec one times to backup logcenter
*/1 * * * *  /crontab/backup.sh >>/var/logs/backup.sh.log 2>&1 &
```

 - 部署报表查看系统
将source_code中的PHP代码部署到服务器上面
修改source_code/applicate/config/db.conf.php里面连接MYSQL和Infobright的信息

相关阅读参考
--

 - NodeJS Socket TCP : [NodeJS Socket TCP API][1]
 - Infobright : [Download Infobright ICE][2]
 - CoolPHP : [CoolPHP GitHub][3]


  [1]: https://nodejs.org/dist/latest-v4.x/docs/api/net.html#net_class_net_socket
  [2]: http://www.infobright.org/index.php/Download/ICE/
  [3]: https://github.com/intril/CoolPHP