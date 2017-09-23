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
defined('ENVIRONMENT') || define('ENVIRONMENT', 'develop');
#是否ajax请求
define('IS_AJAX', isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest" ? true : FALSE);
#是否get请求
define('IS_GET', strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? true : false);
#是否post请求
define('IS_POST', ($_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? true : FALSE);
#项目路径
defined('PROJECT_PATH') or define('PROJECT_PATH', dirname(dirname($_SERVER['DOCUMENT_ROOT'])));
#框架组件路径
defined('FRAMEWORK_PATH') or define('FRAMEWORK_PATH', __DIR__);
#框架组件路径
defined('VENDOR_PATH') or define('VENDOR_PATH', dirname(FRAMEWORK_PATH));
#当前域名
defined('HTTP_HOST') or define('HTTP_HOST', $_SERVER['HTTP_HOST']);
/*框架常量设置 结束*/

#载入函数库
require FRAMEWORK_PATH . '/function.php';


#错误报告级别(默认全部)
if (ENVIRONMENT == 'develop') {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    ini_set('error_log', PROJECT_PATH . '/log/phperror.txt');
} elseif (ENVIRONMENT == 'product') {
    error_reporting(E_ALL ^ E_NOTICE);
    ini_set('display_errors', false);
    ini_set('error_log', PROJECT_PATH . '/log/phperror.txt');
}

#时区设置
date_default_timezone_set('PRC');
try {
    require VENDOR_PATH . '/Aura.Autoload-2.x/src/Loader.php';
    require VENDOR_PATH . '/Pimple-master/src/Pimple/Container.php';
    $container = new \Pimple\Container();
    #注册自动加载类
    $container['loader'] = function ($c) {
        return new \Aura\Autoload\Loader();
    };
    $loader = $container['loader'];
    $loader->register();
    $loader->setPrefixes(require(VENDOR_PATH . '/class_map.php'));

    #注册http请求打包组件
    $container['request'] = function ($c) {
        return new \houduanniu\base\Request($c['config']->all());
    };
    #注册框架配置组件
    $container['config'] = function ($c) {
        return new \houduanniu\base\Config(PROJECT_PATH . '/common/config');
    };

    #注册缓存组件
    $container['cache'] = function ($c) {
        return (new \houduanniu\base\Cache())->setCachePath(PROJECT_PATH . '/cache/');
    };

    #注册curl组件
    $container['curl'] = function ($c) {
        return new \Curl\Curl();
    };

    #注册模版引擎n组件
    $container['template_engine'] = function ($c) {
        return new \League\Plates\Engine();
    };

    #注册验证器组件
    $container['validation'] = function ($c) {
        require VENDOR_PATH . '/overtrue/validation/src/helpers.php';
        $lang = require VENDOR_PATH . '/overtrue/zh-CN/validation.php';
        return new \Overtrue\Validation\Factory(new \Overtrue\Validation\Translator($lang));
    };

    #注册session组件
    $container['session'] = function ($c) {
        return (new \Aura\Session\SessionFactory())->newInstance($_COOKIE);
    };

    #注册session segment组件
    $container['segment'] = function ($c) {
        $session = $c['session'];
        $session->setCookieParams(array('lifetime' => 1800 * 24));
        $segment_key = $c['config']->get('SEGMENT_KEY');
        return $session->getSegment($segment_key);
    };


    #注册路由数据
    $request_data = $container['request']->run();
    $container['request_data'] = $request_data;

    #应用模块常量
    defined('MODULE_NAME') or define('MODULE_NAME', $request_data['module']);
    #应用模块常量
    defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $request_data['controller']);
    #应用模块常量
    defined('ACTION_NAME') or define('ACTION_NAME', $request_data['action']);

    #添加应用类文件加载位置
    $appPath = array(
        PROJECT_PATH . '/' . strtolower($request_data['module']),
        PROJECT_PATH . '/common',
    );
    $loader->addPrefix('app', $appPath);

    #添加应用配置
    if (is_dir(PROJECT_PATH . '/' . MODULE_NAME . '/config')) {
        unset($container['config']);
        $container['config'] = function ($c) {
            $config_path = [
                PROJECT_PATH . '/common/config',
                PROJECT_PATH . '/' . MODULE_NAME . '/config',
            ];
            return new \houduanniu\base\Config($config_path);
        };
    }
    #运行应用
    \houduanniu\base\Application::run($container);
} catch (\Exception $e) {
    $engine = $container['template_engine'];
    $engine->setDirectory(__DIR__ . '/templates/');
    $engine->setFileExtension('tpl');
    $engine->addData([
        'e' => [
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ],
    ]);
    send_http_status($e->getCode());
    die ($engine->render('think_exception'));
};
