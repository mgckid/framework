<?php

/**
 * 控制器基类
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2015/11/3
 * Time: 17:23
 */

namespace houduanniu\web;
use houduanniu\base\Application;
class Controller
{

    function __construct()
    {

    }

    /**
     * ajax返回数据
     * @param $code
     * @param $message
     * @param $data
     */
    public function ajaxReturn($code, $message, $data)
    {
        $return = array(
            'status' => $code,
            'msg' => $message,
            'data' => $data,
        );
        $this->ajaxExit($return);
    }

    /**
     * ajax返回 json
     * @param $data
     */
    public function ajaxExit($data)
    {
        exit(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * ajax返回数据成功
     * @param string $message
     * @param string $data
     */
    public function ajaxSuccess($message = '执行成功', $data = '')
    {
        $message = !empty($message) ? $message : '执行成功';
        $this->ajaxReturn(1, $message, $data);
    }

    /**
     * ajax返回数据失败
     * @param string $message
     * @param string $data
     */
    public function ajaxFail($message = '执行失败', $data = '')
    {
        $message = !empty($message) ? $message : '执行失败';
        $this->ajaxReturn(0, $message, $data);
    }


    /**
     * 页面跳转
     * @param type $url
     */
    public function redirect($url)
    {
        header('location:' . $url);
        exit();
    }

    public function setMessage($msg)
    {
        return Application::getInstance()->setMessage($msg);
    }

    public function getMessage()
    {
        return Application::getInstance()->getMessage();
    }

    public function getInfo($key)
    {
        return Application::getInstance()->getInfo($key);
    }

    public function setInfo($key, $value)
    {
        Application::getInstance()->setInfo($key, $value);
    }

}
