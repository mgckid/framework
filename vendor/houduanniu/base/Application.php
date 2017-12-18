<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/5/26
 * Time: 16:07
 */

namespace houduanniu\base;


use Exceptions\Http\Client\NotFoundException;
use Pimple\Container;

class Application
{
    protected static $instance;
    protected $container;
    protected $message;
    protected $info;

    private function __construct()
    {
    }


    /**
     * 运行应用
     * @access public
     * @author furong
     * @param $config
     * @return void
     * @since  2017年3月22日 16:44:31
     * @abstract
     */
    public static function run($container)
    {
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
        defined('APP_PATH') or define('APP_PATH', $app_path);
        #公共模块路径
        $common_path = $container['config']->has('COMMON_PATH') ? $container['config']->get('COMMON_PATH') : APP_PATH . '/common';
        defined('COMMON_PATH') or define('COMMON_PATH', $common_path);
        $loader = $container['loader'];
        #添加应用类文件加载位置
        $appPath = array(
            APP_PATH,
            COMMON_PATH,
        );
        $loader->addPrefix('app', $appPath);
        #添加公共第三方扩展类夹在位置
        $common_vendor_class_map = COMMON_PATH . '/vendor/class_map.php';
        if (file_exists($common_vendor_class_map)) {
            $class_map_result = require($common_vendor_class_map);
            if (is_array($class_map_result) && !empty($class_map_result)) {
                foreach ($class_map_result as $key => $value) {
                    $loader->addPrefix($key, $value);
                }
            }
        }
        #添加应用第三方扩展类夹在位置
        $app_vendor_class_map = APP_PATH . '/vendor/class_map.php';
        if (file_exists($app_vendor_class_map)) {
            $class_map_result = require($app_vendor_class_map);
            if (is_array($class_map_result) && !empty($class_map_result)) {
                foreach ($class_map_result as $key => $value) {
                    $loader->addPrefix($key, $value);
                }
            }
        }
        #添加应用配置
        if (is_dir(APP_PATH . '/config')) {
            unset($container['config']);
            $container['config'] = function ($c) {
                $config_path = [
                    COMMON_PATH . '/config',
                    APP_PATH . '/config',
                ];
                return new \houduanniu\base\Config($config_path);
            };
        }
        #加载应用依赖脚本
        $require_script = $container['config']->get('REQUIRE_SCRIPT_MAP');
        if (!empty($require_script)) {
            foreach ($require_script as $value) {
                require $value;
            }
        }
        #运行程序
        $controller_name = 'app\\' . self::config()->get('DIR_CONTROLLER') . '\\' . CONTROLLER_NAME . self::config()->get('EXT_CONTROLLER');
        if (!class_exists($controller_name)) {
            throw new NotFoundException('控制器不存在');
        } elseif (!method_exists($controller_name, ACTION_NAME)) {
            throw new NotFoundException('方法不存在');
        } else {
            #执行方法
            call_user_func(array(new $controller_name, ACTION_NAME));
        }
    }

    /**
     * 获取类实例化对象
     * @return $this
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 获取类注册器
     * @return \Pimple\Container
     */
    static public function container()
    {
        if (!self::getInstance()->container) {
            self::getInstance()->container = new Container();
        }
        return self::getInstance()->container;
    }

    /**
     * 设置消息
     * @param $msg
     */
    public static function setMessage($msg)
    {
        self::getInstance()->message = $msg;
    }

    /**
     * 获取消息
     * @return mixed
     */
    public static function getMessage()
    {
        return self::getInstance()->message;
    }

    /**
     * 设置数据
     * @param $name
     * @param null $value
     */
    public static function setInfo($name, $value = NULL)
    {
        self::getInstance()->info[$name] = $value;
    }

    /**
     * 获取数据
     * @param $name
     * @return null
     */
    public static function getInfo($name)
    {
        $return = null;
        if (isset(self::getInstance()->info[$name])) {
            $return = self::getInstance()->info[$name];
        }
        return $return;
    }

    /**
     * 回话组件
     * @return  Config
     */
    static function config()
    {
        return self::container()['config'];
    }


    /**
     * 缓存组件
     * @return  Cache
     */
    static function cache($cache_name = null)
    {
        return self::container()['cache']->setCache($cache_name);
    }


    /**
     * @access public
     * @author furong
     * @return \Overtrue\Validation\Factory
     * @since
     * @abstract
     */
    static function validation()
    {
        return self::container()['validation'];
    }

    /**
     * 模版引擎组件
     * @access public
     * @author furong
     * @return \League\Plates\Engine
     * @since
     * @abstract
     */
    static function templateEngine()
    {
        return self::container()['templateEngine'];
    }

    /**
     * 钩子组件
     * @access public
     * @author furong
     * @return \houduanniu\base\Hooks
     * @since
     * @abstract
     */
    static function hooks()
    {
        return self::container()['hooks'];
    }
}