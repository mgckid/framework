<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/5/26
 * Time: 16:07
 */

namespace houduanniu\base;


use Aura\Session\SessionFactory;
use Aura\Session\Session;
use Aura\Session\Segment;
use Exceptions\Http\Client\NotFoundException;

class Application
{
    public static $instance;
    public $register;
    public $route;
    public $message;
    public $info;

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
    public static function run($register)
    {
        self::getInstance()->register = $register;
        #打包http请求
        self::getInstance()->register->set('request', new Request((new Config(__PROJECT__ . '/common/config'))->all()));
        self::getInstance()->route = self::getInstance()->register->get('request')->run();
        #应用加载
        $appPath = array(
            __PROJECT__ . '/' . strtolower(self::getModule()),
            __PROJECT__ . '/common',
        );
        self::getInstance()->register->get('autoloader')->addPrefix('app', $appPath);

        #载入自定义函数库
        foreach (self::config()->get('LOAD_FILES') as $file) {
            require $file;
        }
        #开启session
//        session_start();
        #运行程序
        $controllerName = 'app\\' . self::config()->get('DIR_CONTROLLER') . '\\' . self::getController() . self::config()->get('EXT_CONTROLLER');
        if (!class_exists($controllerName)) {
            throw new NotFoundException('控制器不存在');
        } elseif (!method_exists($controllerName, self::getAction())) {
            throw new NotFoundException('方法不存在');
        } else {
            #执行方法
            call_user_func(array(new $controllerName, self::getAction()));
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
     * 获取url路由请求模块
     * @return mixed
     */
    public static function getModule()
    {
        return self::getInstance()->route['module'];
    }

    /**
     * 获取url路由请求控制器
     * @return mixed
     */
    public static function getController()
    {
        return self::getInstance()->route['controller'];
    }

    /**
     * 获取url路由请求方法
     * @return mixed
     */
    public static function getAction()
    {
        return self::getInstance()->route['action'];
    }

    /**
     * 获取路由打包数据
     * @param type $name
     * @return type
     */
    public static function getRouter($name = NULL)
    {
        $return = self::getInstance()->route;
        if (!empty($name)) {
            $return = $return[$name];
        }
        return $return;
    }


    public function setMessage($msg)
    {
        $this->message = $msg;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setInfo($name, $value = NULL)
    {
        $this->info[$name] = $value;
    }

    public function getInfo($name)
    {
        if (!isset($this->info[$name])) {
            return NULL;
        }
        return $this->info[$name];
    }

    /**
     * 获取类注册器
     * @return Register
     */
    static public function register()
    {
        return self::getInstance()->register;
    }

    /**
     * 会话组件
     * @return  Session
     */
    static public function session()
    {
        if (!self::register()->has('session')) {
            $session_factory = new SessionFactory();
            self::register()->set('session', $session_factory->newInstance($_COOKIE));
        }
        return self::register()->get('session');
    }

    /**
     * 回话组件
     * @return  Config
     */
    static function config()
    {
        if (!self::register()->has('config')) {
            self::register()->set('config', new Config([__PROJECT__ . '/' . strtolower(self::getModule()) . '/config', __PROJECT__ . '/common/config']));
        }
        return self::register()->get('config');
    }

    /**
     * 缓存组件
     * @return  Cache
     */
    static function cache($cache_name = null)
    {
        if (!self::register()->has('cache')) {
            self::register()->set('cache', (new Cache())->setCachePath(__PROJECT__ . '/cache/'));
        }
        return self::register()->get('cache')->setCache($cache_name);
    }

    /**
     * session 分片
     * @return Segment
     */
    static function segment()
    {
        if (!self::register()->has('segment')) {
            self::session()->setCookieParams(array('lifetime' => 1800 * 24));
            $secure_key = self::config()->get('SEGMENT_KEY');
            self::register()->set('segment', self::session()->getSegment($secure_key));
        }
        return self::register()->get('segment');
    }

}