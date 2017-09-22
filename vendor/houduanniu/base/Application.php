<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/5/26
 * Time: 16:07
 */

namespace houduanniu\base;


use Exceptions\Http\Client\NotFoundException;

class Application
{
    protected static $instance;
    protected $container;

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
        $request_data = $container['request_data'];
        self::getInstance()->container = $container;
        #运行程序
        $controller_name = 'app\\' . self::config()->get('DIR_CONTROLLER') . '\\' . $request_data['controller'] .self::config()->get('EXT_CONTROLLER');
        if (!class_exists($controller_name)) {
            throw new NotFoundException('控制器不存在');
        } elseif (!method_exists($controller_name, $request_data['action'])) {
            throw new NotFoundException('方法不存在');
        } else {
            #执行方法
            call_user_func(array(new $controller_name, $request_data['action']));
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
     * @return Register
     */
    static public function Container()
    {
        return self::getInstance()->container;
    }

    /**
     * 回话组件
     * @return  Config
     */
    static function config()
    {

        return self::Container()['config'];
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
     * curl组件
     * @return \Curl\Curl
     */
    static function curl()
    {
        return self::container()['curl'];
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
     * 会话组件
     * @return  \Aura\Session\Session
     */
    static public function session()
    {
        return self::container()['session'];
    }

    /**
     * session 分片
     * @return Segment
     */
    static function segment()
    {
        return self::container()['segment'];
    }


}