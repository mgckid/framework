<?php
/**
 * 类注册器
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/5/26
 * Time: 15:02
 */

namespace houduanniu\base;


class Register
{
    private $data = array();

    public function get($key)
    {
        return (isset($this->data[$key]) ? $this->data[$key] : null);
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function has($key)
    {
        return isset($this->data[$key]);
    }
} 