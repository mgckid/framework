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

/*框架常量设置 开始*/
#框架运行开发模式
defined('__ENVIRONMENT__') || define('__ENVIRONMENT__', 'develop');
#是否ajax请求
define('IS_AJAX', isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest" ? true : FALSE);
#是否get请求
define('IS_GET', strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? true : false);
#是否post请求
define('IS_POST', ($_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? true : FALSE);
#项目路径
defined('__PROJECT__') or define('__PROJECT__', dirname(dirname($_SERVER['DOCUMENT_ROOT'])));
#框架组件路径
defined('__FRAMEWORK__') or define('__FRAMEWORK__', __DIR__);
#框架组件路径
defined('__VENDOR__') or define('__VENDOR__', dirname(__FRAMEWORK__));
#当前域名
defined('__HOST__') or define('__HOST__', $_SERVER['HTTP_HOST']);
/*框架常量设置 结束*/

#载入函数库
require __FRAMEWORK__ . '/function.php';


#错误报告级别(默认全部)
if (__ENVIRONMENT__ == 'develop') {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    ini_set('error_log', __PROJECT__ . '/log/phperror.txt');
} elseif (__ENVIRONMENT__ == 'product') {
    error_reporting(E_ALL ^ E_NOTICE);
    ini_set('display_errors', false);
    ini_set('error_log', __PROJECT__ . '/log/phperror.txt');
}

#时区设置
date_default_timezone_set('PRC');
try {
    #注册自动加载类
    require __VENDOR__ . '/Aura.Autoload-2.x/src/Loader.php';
    require __VENDOR__ . '/Pimple-master/src/Pimple/Container.php';
    $container = new \Pimple\Container();
    $container['loader'] = function ($c) {
        return new \Aura\Autoload\Loader();
    };
    $loader = $container['loader'];
    $loader->register();
    $loader->setPrefixes(require(__VENDOR__ . '/classMap.php'));

    #注册http请求打包组件
    $container['request'] = function ($c) {
        return new \houduanniu\base\Request($c['config']->all());
    };
    #注册框架配置组件
    $container['config'] = function ($c) {
        return new \houduanniu\base\Config(__PROJECT__ . '/common/config');
    };

    #注册缓存组件
    $container['cache'] = function ($c) {
        return (new \houduanniu\base\Cache())->setCachePath(__PROJECT__ . '/cache/');
    };

    #应用加载
    $request_data = $container['request']->run();
    $appPath = array(
        __PROJECT__ . '/' . strtolower($request_data['module']),
        __PROJECT__ . '/common',
    );
    $loader->addPrefix('app', $appPath);
    \houduanniu\base\Application::run($container);
} catch (\Exception $e) {
    \houduanniu\web\View::getEngine()->setDirectory(__DIR__ . '/templates/');
    \houduanniu\web\View::getEngine()->setFileExtension('tpl');
    \houduanniu\web\View::getEngine()->addData([
        'e' => [
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ],
    ]);
    send_http_status($e->getCode());
    die (\houduanniu\web\View::getEngine()->render('think_exception'));
};
