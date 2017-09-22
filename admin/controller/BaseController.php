<?php

/**
 * Description of BaseController
 *
 * @author Administrator
 */

namespace app\controller;

use houduanniu\web\Controller;
use houduanniu\web\View;
use houduanniu\base\Application;
use app\model\SiteConfigModel;

class BaseController extends Controller
{
    /**
     * @var 站点配置
     */
    public $siteInfo;

    public function __construct()
    {
        $siteConfigModel = new SiteConfigModel();
        $result = $siteConfigModel->getConfigList([], 'name,value');
        $siteInfo = [];
        foreach ($result as $value) {
            $siteInfo[$value['name']] = $value['value'];
        }
        $this->siteInfo = $siteInfo;
    }

    /**
     * 输出模版方法
     * @param type $view
     * @param type $dataData
     */
    public function display($view, $data = array())
    {
        View::setDirectory(__PROJECT__ . '/' . strtolower(MODULE_NAME) . '/' . C('DIR_VIEW') . '/' . C('THEME'));
        View::display($view, $data);
    }



}
