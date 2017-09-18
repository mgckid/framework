<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/9/1
 * Time: 16:22
 */

namespace houduanniu\base;


class Hook
{
    static protected $instance;
    protected $actions = array();

    protected function __construct()
    {

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
     * ads a function to an action hook
     * @param $hook
     * @param $function
     */
    public function add_action($hook, $function)
    {
        $hook = mb_strtolower($hook, 'utf-8');
        // create an array of function handlers if it doesn't already exist
        if (!$this->exists_action($hook)) {
            $this->actions[$hook] = array();
        }
        // append the current function to the list of function handlers
        if (is_callable($function)) {
            $this->actions[$hook][] = $function;
            return TRUE;
        }
        return FALSE;
    }

    /**
     * executes the functions for the given hook
     * @param string $hook
     * @param array $params
     * @return boolean true if a hook was setted
     */
    public function do_action($hook, $params = NULL)
    {
        $hook = mb_strtolower($hook, 'utf-8');
        if (isset($this->actions[$hook])) {
            // call each function handler associated with this hook
            foreach ($this->actions[$hook] as $function) {
                $function = explode('::',$function);
                $function[0] = new $function[0];
                if (is_array($params)) {
                    call_user_func_array($function, $params);
                } else {
                    call_user_func($function);
                }
                //cant return anything since we are in a loop! dude!
            }
            return TRUE;
        }
        return FALSE;
    }

    /**
     * gets the functions for the given hook
     * @param string $hook
     * @return mixed
     */
    public function get_action($hook)
    {
        $hook = mb_strtolower($hook, 'utf-8');
        return (isset($this->actions[$hook])) ? $this->actions[$hook] : FALSE;
    }

    /**
     * check exists the functions for the given hook
     * @param string $hook
     * @return boolean
     */
    public function exists_action($hook)
    {
        $hook = mb_strtolower($hook, 'utf-8');
        return (isset($this->actions[$hook])) ? TRUE : FALSE;
    }
} 