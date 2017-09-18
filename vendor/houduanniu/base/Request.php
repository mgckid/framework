<?php

/**
 * Created by PhpStorm.
 * User: mgckid
 * Date: 2015/11/4 0004
 * Time: 下午 23:33
 */

namespace houduanniu\base;

class Request
{

    private $config = array(
        'URL_MODE' => 2,
        'DEFAULT_MODULE' => 'Home',
        'DEFAULT_CONTROLLER' => 'Index',
        'DEFAULT_ACTION' => 'index',
        'VAR_MODULE' => 'm',
        'VAR_CONTROLLER' => 'c',
        'VAR_ACTION' => 'a',
        'VAR_ROUTE' => 'route',
        'MAIN_DOMAIN'=>'',
        'SUB_DOMAIN_OPEN' => false,
        'SUB_DOMAIN_RULES' => [
            'www' => 'home'
        ]
    );

    public function __construct($config = array())
    {
        $this->config = !empty($config) ? array_merge($this->config, $config) : $this->config;
//        print_g($this->config);
    }

    public function __get($name)
    {
        return $this->config[$name];
    }


    /**
     * 打包参数
     * @return $this
     */
    public function run()
    {
        $data = [];
        #url自动识别选择打包方式
        if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
            $data = $this->dispatchByPathinfo($_SERVER['PATH_INFO']);
        } elseif (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
            if (false !== strpos($_SERVER['QUERY_STRING'], $this->config['VAR_ROUTE'] . '=')) {
                $data = $this->dispatchByCompromise($_SERVER['QUERY_STRING']);
            } elseif (isset($_REQUEST[$this->config['VAR_MODULE']]) || isset($_REQUEST[$this->config['VAR_CONTROLLER']]) || isset($_REQUEST[$this->config['VAR_ACTION']])) {
                $data = $this->dispatchByDynamic();
            }
        }
        #指定打包方式
        if (empty($data)) {
            switch ($this->URL_MODE) {
                #动态模式
                case 0:
                    $data = $this->dispatchByDynamic();
                    break;
                #pathinfo 模式
                case 1:
                    $request = (isset($_SERVER['PATH_INFO']) and !empty($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : '';
                    $data = $this->dispatchByPathinfo($request);
                    break;
                #兼容模式
                case 2:
                    $request = (isset($_SERVER['QUERY_STRING']) and !empty($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
                    $data = $this->dispatchByCompromise($request);
                    break;
            }
        }
        if (empty($data['module'])) {
            if ($this->config['SUB_DOMAIN_OPEN']) {
                $mainDomain = $this->config['MAIN_DOMAIN'];
                $subMain = strtolower(trim(str_replace($mainDomain, '', __HOST__), '.'));
                $data['module'] = $this->config['SUB_DOMAIN_RULES'][$subMain];
            } else {
                $data['module'] = lcfirst($this->DEFAULT_MODULE);
            }
        }
        $data['controller'] = empty($data['controller']) ? ucfirst($this->DEFAULT_CONTROLLER) : ucfirst($data['controller']);
        $data['action'] = empty($data['action']) ? lcfirst($this->DEFAULT_ACTION) : lcfirst($data['action']);
        return $data;
    }


    public function dispatchByPathinfo($request = '')
    {
        $data = array(
            'module' => '',
            'controller' => '',
            'action' => '',
        );
        $path = explode('/', trim($request, '/'));
        if (!$path) {
            return $data;
        }
        foreach ($data as $k => $v) {
            $data[$k] = array_shift($path);
        }
        $param = array();
        if ($path) {
            $key = array();
            $value = array();
            foreach ($path as $k => $v) {
                if ($k % 2 == 0) {
                    $key[] = $v;
                } else {
                    $value[] = $v;
                }
            }
            if (count($key) > count($value)) {
                for ($i = 0; $i <= count($key) - count($value); $i++) {
                    $value[] = '';
                }
            }
            $param = array_combine($key, $value);
        }
        $_GET = array_merge($_GET, $param);
        $_REQUEST = array_merge($_REQUEST, $param);
        return $data;
    }

    /**
     * 兼容模式打包url参数
     * @access public
     * @author furong
     * @param $request
     * @return array
     * @since
     * @abstract
     */
    public function dispatchByCompromise($request = '')
    {
        $data = array(
            'module' => '',
            'controller' => '',
            'action' => '',
        );
        $request = trim($request, '/,?');
        if (!empty($request)) {
            $_request = explode('&', $request);
            unset($request);
            foreach ($_request as $value) {
                if(empty($value)){
                    continue;
                }
                $param = explode('=', $value);
                $request[$param[0]] = $param[1];
            }
            $route = explode('/', $request[$this->config['VAR_ROUTE']]);
            if (!empty($route)) {
                if (empty($this->config['SUB_DOMAIN_OPEN']) and empty($this->DEFAULT_MODULE)) {
                    $data['module'] = current(array_splice($route, 0, 1));
                }
            }
            if (!empty($route)) {
                $controller = array_splice($route, 0, 1);
                $data['controller'] = count($controller) == 1 ? current($controller) : join('/', $controller);
            }
            if (!empty($route)) {
                $data['action'] = current(array_splice($route, 0, 1));
            }
            unset($request[$this->config['VAR_ROUTE']]);
            array_merge($_REQUEST, $request);
            array_merge($_GET, $request);
        }
        return $data;
    }


    /**
     * 打包url参数by动态传参模式
     * @param type $request
     * @return type
     */
    public function dispatchByDynamic()
    {
        $data = array(
            'module' => '',
            'controller' => '',
            'action' => '',
        );
        if (isset($_REQUEST[$this->VAR_MODULE]) && !empty($_REQUEST[$this->VAR_MODULE])) {
            $data['module'] = $_REQUEST[$this->VAR_MODULE];
            unset($_GET[$this->VAR_MODULE]);
            unset($_REQUEST[$this->VAR_MODULE]);
        }
        if (isset($_REQUEST[$this->VAR_CONTROLLER]) && !empty($_REQUEST[$this->VAR_CONTROLLER])) {
            $data['controller'] = $_REQUEST[$this->VAR_CONTROLLER];
            unset($_GET[$this->VAR_CONTROLLER]);
            unset($_REQUEST[$this->VAR_CONTROLLER]);
        }
        if (isset($_REQUEST[$this->VAR_ACTION]) && !empty($_REQUEST[$this->VAR_ACTION])) {
            $data['action'] = $_REQUEST[$this->VAR_ACTION];
            unset($_GET[$this->VAR_ACTION]);
            unset($_REQUEST[$this->VAR_ACTION]);
        }
        return $data;
    }

    public function config($name, $value = NULL)
    {
        if (is_array($name)) {
            array_walk($name, function ($v, $k) {
                $this->config[$k] = $v;
            });
        } else {
            if (array_key_exists($name, $this->config)) {
                $this->config[$name] = $value;
            }
        }
    }

}
