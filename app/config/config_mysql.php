<?php

return [
    'dsn'           => "mysql:host=localhost;dbname=kawe14;",
    'username'      => "root",
    'password'      => "secret", 
    'driver_options'=> [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"], 
    'table_prefix'  => "phpmvc_",
    'verbose'       => true,
    'debug_connect' => 'true',
];
