<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/4
 * Time: 12:41
 */
if (ENVIRONMENT == 'develop') {
    return [
        //链接
        'MAIN_DOMAIN' => 'fff.me',
        'HOME_URL' => 'http://www.fff.me',
        'ADMIN_URL' => 'http://admin.fff.me',
        'API_URL' => 'http://api.fff.me',
        //数据库配置
        'DB' => [
            'default' => array(
                'connection_string' => 'mysql:host=127.0.0.1;dbname=houduanniu_dev;port=3306',
                'id_column' => 'id',
                'id_column_overrides' => array(),
                'error_mode' => \PDO::ERRMODE_EXCEPTION,
                'username' => 'root',
                'password' => 'fr1314520',
                'driver_options' => [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    \PDO::ATTR_PERSISTENT => true,
                ],
                'logging' => true,
                'caching' => false,
                'caching_auto_clear' => false,
                'return_result_sets' => false
            ),
        ],
    ];
}elseif(ENVIRONMENT == 'product') {
    return [
        //链接
        'MAIN_DOMAIN' => 'houduanniu.com',
        'HOME_URL' => 'http://blog.houduanniu.com',
        'ADMIN_URL' => 'http://admin.houduanniu.com',
        'API_URL' => 'http://api.houduanniu.com',
        //数据库配置
        'DB' => [
            'default' => array(
                'connection_string' => 'mysql:host=127.0.0.1;dbname=houduanniu_pro;port=3306',
                'id_column' => 'id',
                'id_column_overrides' => array(),
                'error_mode' => \PDO::ERRMODE_EXCEPTION,
                'username' => 'root',
                'password' => 'fr1314520',
                'driver_options' => [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    \PDO::ATTR_PERSISTENT => true,
                ],
                'logging' => true,
                'caching' => false,
                'caching_auto_clear' => false,
                'return_result_sets' => false
            ),
        ],
    ];
}