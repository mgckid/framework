<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/3/22
 * Time: 15:29
 */

return array(
    /**
     * 框架核心库文件非必要不要去修改
     */
    /***************************核心库文件 开始****************************/
    #后端牛框架库文件
    'houduanniu' => __VENDOR__ . '/houduanniu',

    #session 会话管理类
    'Aura\Session' => __VENDOR__ . '/Aura.Session-2.x/src',

    #自动加载库
    'Aura\Autoload' => __VENDOR__ . '/Aura.Autoload-2.x/src',

    #配置类(Config is a lightweight configuration file loader that supports PHP, INI, XML, JSON, and YAML files)
    'Noodlehaus' => __VENDOR__ . '/hassankhan/config/src',

    #idiorm\orm类
    'idiorm\orm' => __VENDOR__ . '/idiorm-master/src/idiorm/orm',

    #模版引擎
    'League\Plates' => __VENDOR__ . '/thephpleague/plates/src',

    #验证类(Laravel Validation 简化无依赖版)
    'Overtrue\Validation' => __VENDOR__ . '/overtrue/validation/src',

    #上传类(File uploads with validation and storage strategies)
    'Upload' => __VENDOR__ . '/Upload-master/src/Upload/',

    #图片处理类(PHP Image Manipulation )
    'Intervention\Image' => __VENDOR__ . '/image/src/Intervention/Image',

    #Guzzle，可扩展的PHP HTTP客户端 http://guzzlephp.org/
    'GuzzleHttp' => __VENDOR__ . '/guzzle-master/src',
    /****************************核心库文件 结束***************************/

    #基于词库的中文转拼音优质解决方案 http://overtrue.me/pinyin
    'Overtrue\Pinyin' => __VENDOR__ . '/overtrue/pinyin/src',

    #QueryList是基于phpQuery的无比强大的PHP采集工具
    'QL' => __VENDOR__ . '/QueryList',

    /*phpFastCache 是一个开源的 PHP 缓存库，只提供一个简单的 PHP 文件，可方便集成到已有项目，支持多种缓存方法，
    包括：apc, memcache, memcached, wincache, files, pdo and mpdo。可通过简单的 API 来定义缓存的有效时间。*/
    'phpFastCache' => __VENDOR__ . '/phpfastcache/src/phpFastCache',

    #PSR-6 缓存接口规范
    'Psr\Cache' => __VENDOR__ . '/phpfastcache/bin/legacy/Psr/Cache/src',

    #utilphp是一个使用静态方法调用的类,收集了实用且每天都会使用的函数
    'utilphp' => __VENDOR__ . '/utilphp/src/utilphp',

    #简单的 PHP 类注释解析类
    'DocBlockReader' => __VENDOR__ . '/php-simple-annotations/src/DocBlockReader',

    #whoops is an error handler framework for PHP(错误处理类)
    'Whoops' => __VENDOR__ . '/whoops-master/src/Whoops',

    #异常类 A small library that aims at supplementing default exceptions in PHP
    'Exceptions' => __VENDOR__ . '/standard-exceptions-master/Exceptions'
);
