<?php

/**
 * Description of BaseController
 *
 * @author Administrator
 */

namespace app\controller;

use houduanniu\web\Controller;
use houduanniu\web\View;
use app\model\SiteConfigModel;

class BaseController extends Controller
{
    /**
     * @var 站点配置
     */
    public $siteInfo;

    public function __construct()
    {
        require(PROJECT_PATH . '/common/vendor/function.php');
    }

    public function getSiteInfo()
    {
        return $this->getInfo('siteInfo');
    }

    public function setSiteInfo()
    {
        $siteConfigModel = new SiteConfigModel();
        $result = $siteConfigModel->getConfigList([], 'name,value');
        $siteInfo = [];
        foreach ($result as $value) {
            $siteInfo[$value['name']] = $value['value'];
        }
        $this->setInfo('siteInfo', $siteInfo);
    }

    /**
     * 输出模版方法
     * @param type $view
     * @param type $dataData
     */
    public function display($view, $data = array())
    {
        View::setDirectory(PROJECT_PATH . '/' . strtolower(MODULE_NAME) . '/' . C('DIR_VIEW') . '/');
        View::display($view, $data);
    }


}
