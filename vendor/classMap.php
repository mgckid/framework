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

    #自动加载库
    'Aura\Autoload' => __VENDOR__ . '/Aura.Autoload-2.x/src',

    #配置类(Config is a lightweight configuration file loader that supports PHP, INI, XML, JSON, and YAML files)
    'Noodlehaus' => __VENDOR__ . '/hassankhan/config/src',

    #idiorm\orm类
    'idiorm\orm' => __VENDOR__ . '/idiorm-master/src/idiorm/orm',

    #模版引擎
    'League\Plates' => __VENDOR__ . '/thephpleague/plates/src',
    /****************************核心库文件 结束***************************/
    #A small PHP 5.3 dependency injection container http://pimple.sensiolabs.org
    'Pimple' => __VENDOR__ . '/Pimple-master/src/Pimple',
);
