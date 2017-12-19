<?php
/**
 * 框架启动文件
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/5/26
 * Time: 14:44
 */
#设置页面字符编码
header("content-type:text/html; charset=utf-8");
try {
    #载入函数库
    require __DIR__ . '/function.php';

    /*框架常量设置 开始*/
    #是否ajax请求
    define('IS_AJAX', isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest" ? true : FALSE);
    #是否get请求
    define('IS_GET', strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? true : false);
    #是否post请求
    define('IS_POST', ($_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? true : FALSE);
    #框架组件路径
    defined('FRAMEWORK_PATH') or define('FRAMEWORK_PATH', __DIR__);
    #应用目录（默认为入口文件所在目录）
    defined('APP_PATH') or define('APP_PATH', $_SERVER['DOCUMENT_ROOT']);
    #框架运行开发模式
    defined('ENVIRONMENT') or define('ENVIRONMENT', 'develop');
//    #项目路径 PROJECT_PATH
//    defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['DOCUMENT_ROOT']));
    #框架组件路径
    defined('VENDOR_PATH') or define('VENDOR_PATH', dirname(FRAMEWORK_PATH));
    #当前域名
    defined('HTTP_HOST') or define('HTTP_HOST', $_SERVER['HTTP_HOST']);


    #错误处理设置
    {
        set_error_handler('errorHandle');
        #错误报告级别(默认全部)
        if (ENVIRONMENT == 'develop') {
            error_reporting(E_ALL);
            ini_set('display_errors', true);
        } elseif (ENVIRONMENT == 'product') {
            error_reporting(E_ALL ^ E_NOTICE);
            ini_set('display_errors', false);
            ini_set('log_errors', true);
            ini_set('error_log', APP_PATH . '/log/php_error.txt');
        }
    }
    require VENDOR_PATH . '/Aura.Autoload-2.x/src/Loader.php';
    #自动加载设置
    $loader = new \Aura\Autoload\Loader();
    $loader->register();
    $loader->setPrefixes(require(VENDOR_PATH . '/class_map.php'));
    #注册框架配置组件
    $common_config = is_dir(APP_PATH . '/config') ? APP_PATH . '/config' : [];
    $config = new \houduanniu\base\Config($common_config);
    date_default_timezone_set($config->get('timezone_set'));

    $container = \houduanniu\base\Application::container();
    $container['loader'] = $loader;
    $container['config'] = $config;
    #注册缓存组件
    $container['cache'] = $config->get('cache');
    #注册模版引擎组件
    $container['templateEngine'] = $config->get('templateEngine');
    #注册验证器组件
    $container['validation'] = $config->get('validation');
    #注册http请求打包组件
    $container['request'] = $config->get('request');
    #注册路由数据
    $container['request_data'] = $config->get('request_data');
    #注册钩子组件
    $container['hooks'] = $config->get('hooks');

    #公共模块路径
    $common_path = $config->has('COMMON_PATH') ? $config->get('COMMON_PATH') : APP_PATH . '/common';
    defined('COMMON_PATH') or define('COMMON_PATH', $common_path);
    #当前模块名称常量
    defined('COMMON_MODULE') or define('COMMON_MODULE', 'common');
    #当前模块名称常量
    defined('MODULE_NAME') or define('MODULE_NAME', $container['request_data']['module']);
    #当前控制器名称常量
    defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $container['request_data']['controller']);
    #当前方法名称常量
    defined('ACTION_NAME') or define('ACTION_NAME', $container['request_data']['action']);

    #当前模块路径
    $app_path = $container['config']->has(strtoupper(MODULE_NAME) . '_PATH') ? $container['config']->get(strtoupper(MODULE_NAME) . '_PATH') : APP_PATH . '/' . strtolower(MODULE_NAME);
    defined('MODULE_PATH') or define('MODULE_PATH', $app_path);
    #运行应用
    \houduanniu\base\Application::run($container);
} catch (\Exception $e) {
    errorPage($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
};


