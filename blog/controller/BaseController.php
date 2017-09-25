<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/3/12
 * Time: 9:32
 */

namespace app\controller;


use app\model\SiteSetModel;
use Curl\Curl;
use houduanniu\web\Controller;
use houduanniu\web\View;
use houduanniu\base\Application;

class BaseController extends Controller
{
    #面包屑导航
    protected $crumbHtml;
    public $imageSize;


    function __construct()
    {
        parent::__construct();
        $this->imageSize = C('IMAGE_SIZE');
        $this->setInfo('siteInfo', $this->getSiteInfo());
    }

    /**
     * 面包屑导航
     * @param $crumb
     */
    public function crumb($crumbs = array())
    {
        $crumbsHtml = '';
        if (!empty($crumbs)) {
            $crumbsHtml .= '<li><a href="' . U('/Index/index') . '">首页</a></li>';
            $n = 0;
            foreach ($crumbs as $key => $value) {
                $n++;
                $link_s = !empty($value) ? '<a href="' . $value . '">' : '';
                $link_e = !empty($value) ? '</a>' : '';
                if ($n == count($crumbs)) {
                    $crumbsHtml .= '<li class="active color4">' . $key . '</li>';
                } else {
                    $crumbsHtml .= '<li>' . $link_s . $key . $link_e . '</li>';
                }
            }
        }
        //赋值给公共变量
        $this->crumbHtml = $crumbsHtml;
    }

    /**
     * @return \Curl\Curl
     */
    public function curl()
    {
        $container = Application::container();
        if (ENVIRONMENT == 'develop') {
            $container['curl']->setOpt(CURLOPT_PROXY, '127.0.0.1:7777');
        }
        return $container['curl'];
    }

    /**
     * 获取站点信息
     */
    public function getSiteInfo()
    {
        $result = $this->apiRequest('Site/siteConfig', [], 'Api');
        $siteInfo = [];
        foreach ($result['data'] as $value) {
            $siteInfo[$value['name']] = $value['value'];
        }
        return $siteInfo;
    }

    public function apiRequest($url, $data = [], $mode = 'Api', $method = 'get')
    {

        if ($mode == 'Api') {
            $host = C('API_URL');
        }
        if (empty($host)) {
            trigger_error('接口地址不存在');
        }
        #返回缓存内容
        $cache_key = md5(json_encode($data));
        $cache_name = $url;
        if ($method == 'get') {
            if (Application::cache($cache_name)->isCached($cache_key)) {
                return Application::cache($cache_name)->retrieve($cache_key);
            }
        }
        $curl = $this->curl();
        $header = [];
        if ($method == 'get') {
            $url = $host . U($url, $data);
            $response = $curl->get($url);
        }
        if (!is_object($response)) {
            trigger_error('接口请求错误');
        }
        $result = json_decode(json_encode($response), true);;
        #缓存数据
        if ($method == 'get') {
            if (isset($result['cached']) && $result['cached']) {
                Application::cache($cache_name)->store($cache_key, $result, 300);
                Application::cache($cache_name)->eraseExpired();
            }
        }
        return $result;
    }

    /**
     * 输出模版方法
     * @param type $view
     * @param type $data
     */
    public function display($view, $data = array(), $seoInfo = array())
    {
        #站点信息
        {
            $siteInfo = $this->getSiteInfo();
            $siteInfo['title'] = !empty($seoInfo['title']) ? $seoInfo['title'] . '_' . $siteInfo['site_name'] : $siteInfo['site_name'];
            $siteInfo['keywords'] = !empty($seoInfo['keywords']) ? $seoInfo['keywords'] : $siteInfo['site_keywords'];
            $siteInfo['description'] = !empty($seoInfo['description']) ? $seoInfo['description'] : $siteInfo['site_description'];

            $reg['siteInfo'] = $siteInfo;
            $reg['crumbs'] = $this->crumbHtml;
        }
        #友情链接
        {

            $result = $this->apiRequest('Site/flink', [], 'Api');
            $reg['flink'] = $result['data'];
        }
        #网站导航
        {
            $result = $this->apiRequest('Post/siteNavigation', [], 'Api');
            $reg['siteNavgation'] = $result['data'];
        }
        View::addData($reg);
        View::setDirectory(PROJECT_PATH . '/' . strtolower(MODULE_NAME) . '/' . C('DIR_VIEW'));
        View::display($view, $data);
    }


} 