<?php

/**
 * 自动加载类
 * 2016年2月3日 09:46:40
 */

namespace houduanniu\base;

class Autoload
{

    private $classExtensions = '.php';
    private $classRoot;
    private $map;
    static private $instance;

    public function __construct()
    {
        spl_autoload_register(array($this, 'classLoader'));
    }

    /**
     * 加载类文件
     * @param type $className
     * @return boolean
     */
    public function classLoader($className)
    {
        if (isset($this->map[$className])) {
            return true;
        }
        $classPath = str_replace('\\', '/', $className);
        var_dump($className);die();
        foreach ($this->classRoot as $key => $value) {
            if (is_array($value)) {
                $fullClassPath = rtrim($value[0], $value[1]) . $classPath . $this->classExtensions;
            } else {
                $fullClassPath = $value . '/' . $classPath . $this->classExtensions;
            }
            if (is_file($fullClassPath)) {
                require $fullClassPath;
                $this->map[$className] = $fullClassPath;
                return true;
            }
        }
    }

    /**
     * 注册类文件加载所在根目录
     * @param $classRoot
     * @param $Prefix
     */
    static public function register($classRoot, $prefix)
    {
        self::getInstance()->classRoot[$prefix][] = $classRoot;
    }

    /**
     * 获取类自身实例化对象
     * @return type
     */
    static protected function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}
