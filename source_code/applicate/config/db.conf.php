<?php
/**
 * Database settings are case sensitive.
 * To set collation and charset of the db connection, use the key 'collate' and 'charset'
 */
$db = array(
    'backend' => array(
        'host'     => '172.24.180.196',
        'port'     => 3306,
        'dbname'   => 'monitor_config',
        'user'     => 'root',
        'password' => '123',
        'driver'   => 'mysqli',
        'pconnect' => false,
        'charset'  => 'utf8'
    ),
    'infobright' => array(
        'host'     => '172.24.180.196',
        'port'     => 5029,
        'dbname'   => 'BH_log_center',
        'user'     => 'root',
        'password' => '123',
        'driver'   => 'mysqli',
        'pconnect' => false,
        'charset'  => 'utf8'
    ),
);
