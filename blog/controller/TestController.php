<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/8/28
 * Time: 15:37
 */

namespace app\controller;


use houduanniu\base\Hook;

class TestController extends BaseController
{
    public function index()
    {
        //添加钩子
        Hook::getInstance()->add_action('test_action','print_g');
        //执行钩子
        hook::getInstance()->do_action('test_action',$this->siteInfo);//也可以使用 Hook::do_action();
      //  $this->display('Test/index', []);
    }
}
