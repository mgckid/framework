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
        $config = $container['config'];
        $request_data = $container['request_data'];
        $loader = $container['loader'];
        #载入应用
        $appPath = array(
            __PROJECT__ . '/' . strtolower($request_data['module']),
            __PROJECT__ . '/common',
        );
        $loader->addPrefix('app', $appPath);

        #运行程序
        $controller_name = 'app\\' . $config->get('DIR_CONTROLLER') . '\\' . $request_data['controller'] . $config->get('EXT_CONTROLLER');
        if (!class_exists($controller_name)) {
            throw new NotFoundException('控制器不存在');
        } elseif (!method_exists($controller_name, $request_data['action'])) {
            throw new NotFoundException('方法不存在');
        } else {
            #执行方法
            self::getInstance()->container = $container;
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
    static public function getContainer()
    {
        return self::getInstance()->container;
    }
}