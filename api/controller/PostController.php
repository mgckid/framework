<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/21
 * Time: 0:01
 */

namespace app\controller;


use app\logic\BaseLogic;
use app\model\CmsPostExtendAttributeModel;
use houduanniu\api\Controller;
use app\model\CmsPostModel;
use houduanniu\base\Page;
use app\model\CmsCategoryModel;
use app\model\DictionaryModelModel;

class PostController extends Controller
{
    public function tagList()
    {
        $cmsPostExtendAttributeModel = new CmsPostExtendAttributeModel();
        $orm = $cmsPostExtendAttributeModel->orm()->where('field', 'post_tag');
        $result = $cmsPostExtendAttributeModel->getAllRecord($orm, 'value');
        $tags = [];
        foreach ($result as $value) {
            $tags = array_merge($tags, explode(',', $value['value']));
        }
        $tags = array_values(array_unique($tags));
        $this->response($tags, self::S200_OK, null, true);
    }

    public function hotPost()
    {
        $cmsPostModel = new CmsPostModel();
        $rules = [
            'page_size' => 'required|integer',
        ];
        $map = [
            'page_size' => '每页记录条数',
        ];
        $validate = $cmsPostModel->validate()->make($_REQUEST, $rules, [], $map);
        if (false == $validate->passes()) {
            $this->response(null, self::S400_BAD_REQUEST, $validate->messages()->first());
        }
        $page_size = $_REQUEST['page_size'];
        $result = $cmsPostModel->getRecordList('', 0, $page_size, false, 'click');
        $list = [];
        foreach ($result as $key => $value) {
            $post = $cmsPostModel->getRecordInfoById($value['id']);
            $list[] = $post;
        }
        $this->response($list, self::S200_OK, null, true);
    }

    public function tags()
    {
        $cmsPostModel = new CmsPostModel();
        $cmsPostExtendAttributeModel = new CmsPostExtendAttributeModel();
        $rules = [
            'p' => 'required|integer',
            'page_size' => 'required|integer',
            'tag_name' => 'required'
        ];
        $map = [
            'p' => '当前页数',
            'page_size' => '每页记录条数',
            'tag_name' => '标签名称'
        ];
        $validate = $cmsPostModel->validate()->make($_REQUEST, $rules, [], $map);
        if (false == $validate->passes()) {
            $this->response(null, self::S400_BAD_REQUEST, $validate->messages()->first());
        }
        $p = $_REQUEST['p'];
        $page_size = $_REQUEST['page_size'];
        $tag_name = $_REQUEST['tag_name'];
        $orm = $cmsPostExtendAttributeModel->orm()->where('field', 'post_tag')->where_like('value', '%' . $tag_name . '%');
        $count = $cmsPostExtendAttributeModel->getRecordList($orm, '', '', true);
        $page = new Page($count, $p, $page_size);
        $result = $cmsPostExtendAttributeModel->getRecordList($orm, $page->getOffset(), $page->getPageSize(), false);
        $list = [];
        $cmsCategoryModel = new CmsCategoryModel();
        foreach ($result as $key => $value) {
            $post = $cmsPostModel->getRecordInfoByPostid($value['post_id']);
            $category_result = $cmsCategoryModel->getRecordInfoById($post['category_id']);
            $post['category_name'] = $category_result['category_name'];
            $post['category_alias'] = $category_result['category_alias'];
            $list[] = $post;
        }
        $return = [
            'count' => $count,
            'list' => $list,
        ];
        $this->response($return, self::S200_OK, null, true);
    }

    public function relatedArticles()
    {
        $cmsPostModel = new CmsPostModel();
        $cmsPostExtendAttributeModel = new CmsPostExtendAttributeModel();
        $rules = [
            'post_id' => 'required|numeric',
            'page_size' => 'required|integer',
        ];
        $map = [
            'post_id' => '内容id',
            'page_size' => '列表记录数'
        ];
        $validate = $cmsPostModel->validate()->make($_REQUEST, $rules, [], $map);
        if (false == $validate->passes()) {
            $this->response(null, self::S400_BAD_REQUEST, $validate->messages()->first());
        }
        $post_id = $_REQUEST['post_id'];
        $page_size = $_REQUEST['page_size'];
        $post_result = $cmsPostModel->getRecordInfoByPostid($post_id);
        if (!$post_result) {
            $this->response(null, self::S400_BAD_REQUEST, '文档不存在');
        }
        $tag = explode(',', $post_result['post_tag']);
        $post_ids = [];
        foreach ($tag as $value) {
            $orm = $cmsPostExtendAttributeModel->orm()->where(['field' => 'post_tag', 'value' => $value]);
            $result = $cmsPostExtendAttributeModel->getAllRecord($orm, 'post_id');
            if ($result) {
                $result = array_column($result, 'post_id');
                $post_ids = array_merge($post_ids, $result);
            }
        }
        if (count($post_ids) < $page_size) {
            $page_size = $page_size - count($post_ids);
            $orm = $cmsPostModel->orm()->where('category_id', $post_result['category_id']);
            if($post_ids){
               $orm = $orm ->where_not_in('post_id', $post_ids);
            }
            $result = $cmsPostModel->getRecordList($orm, 0, $page_size);
            if ($result) {
                $result = array_column($result, 'post_id');
                $post_ids = array_merge($post_ids, $result);
            }
        } else {
            $post_ids = array_slice($post_ids, 0, 6);
        }
        $result = [];
        $orm = $cmsPostModel->orm()->where_in('post_id', $post_ids);
        $result = $cmsPostModel->getAllRecord($orm);
        foreach ($result as $key => $value) {
            $extend_data = $cmsPostModel->getPostExtendAttrbute($value['post_id']);
            if (!empty($extend_data)) {
                $result[$key] = array_merge($value, $extend_data);
            }
        }
        $this->response($result, self::S200_OK, '', true);
    }

    public function siteNavigation()
    {
        $model = new CmsCategoryModel();
        $dictionaryModelModel = new DictionaryModelModel();
        $cateResult = $model->getAllRecord();
        $this->response($cateResult, self::S200_OK, null, true);
    }

    public function category()
    {
        $cmsCategoryModel = new CmsCategoryModel();
        $rules = [
            'category_alias' => 'required',
        ];
        $map = [
            'category_alias' => '栏目标识',
        ];
        $validate = $cmsCategoryModel->validate()->make($_REQUEST, $rules, [], $map);
        if (false == $validate->passes()) {
            $this->response(null, self::S400_BAD_REQUEST, $validate->messages()->first());
        }
        $category_alias = $_REQUEST['category_alias'];
        #获取栏目信息
        {
            $logic = new BaseLogic();
            $orm = $cmsCategoryModel->orm()->where('category_alias', $category_alias);
            $category_result = $cmsCategoryModel->getRecordInfo($orm);
            $model_result = $logic->getModelInfo($category_result['model_id']);
            $category_result['dictionary_value'] = $model_result['dictionary_value'];
        }
        $return = [
            'category_info' => $category_result,
        ];
        $this->response($return, self::S200_OK, null, true);

    }

    public function latestPost()
    {
        $cmsPostModel = new CmsPostModel();
        $rules = [
            'dictionary_value' => 'required|not_in:page',
            'page_size' => 'required|integer',
            'p' => 'required|integer',
        ];
        $map = [
            'dictionary_value' => '文档模型',
            'p' => '当前页数',
            'page_size' => '每页记录条数',
        ];
        $validate = $cmsPostModel->validate()->make($_REQUEST, $rules, [], $map);
        if (false == $validate->passes()) {
            $this->response(null, self::S400_BAD_REQUEST, $validate->messages()->first());
        }
        $dictionary_value = $_REQUEST['dictionary_value'];
        $p = isset($_REQUEST['p']) && !empty($_REQUEST['p']) ? intval($_REQUEST['p']) : 0;
        $page_size = isset($_REQUEST['page_size']) && !empty($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 0;

        $orm = $cmsPostModel->orm()->table_alias('p')->right_join('dictionary_model', ['p.model_id', '=', 'm.id'], 'm')->where(['m.dictionary_value' => $dictionary_value]);
        $field = 'p.*,m.dictionary_value';
        $count = $cmsPostModel->getRecordList($orm, '', '', true);
        $page = new Page($count, $p, $page_size);
        $result = $cmsPostModel->getRecordList($orm, $page->getOffset(), $page->getPageSize(), false, 'p.id', 'desc', $field);

        $list = [];
        $cmsCategoryModel = new CmsCategoryModel();
        foreach ($result as $key => $value) {
            $post = $cmsPostModel->getRecordInfoById($value['id']);
            $category_result = $cmsCategoryModel->getRecordInfoById($post['category_id']);
            $post['category_name'] = $category_result['category_name'];
            $post['category_alias'] = $category_result['category_alias'];
            $list[] = $post;
        }
        $return = [
            'count' => $count,
            'list' => $list,
        ];
        $this->response($return, self::S200_OK, null, true);
    }

    public function categoryPostList()
    {
        $cmsCategoryModel = new CmsCategoryModel();
        $map = [
            'category_id' => '栏目id',
            'p' => '当前页数',
            'page_size' => '每页记录条数',
        ];
        $rules = [
            'category_id' => 'required|integer',
        ];
        $validate = $cmsCategoryModel->validate()->make($_REQUEST, $rules, [], $map);
        if (false == $validate->passes()) {
            $this->response(null, self::S400_BAD_REQUEST, $validate->messages()->first());
        }
        $category_id = isset($_REQUEST['category_id']) && !empty($_REQUEST['category_id']) ? intval($_REQUEST['category_id']) : 0;
        $orm = $cmsCategoryModel->orm()->table_alias('c')->right_join('dictionary_model', ['c.model_id', '=', 'm.id'], 'm')->where(['c.id' => $category_id]);
        $field = 'c.*,m.dictionary_value';
        $model_result = $cmsCategoryModel->getRecordInfo($orm, $field);
        if ($model_result['dictionary_value'] == 'article') {
            $rules = [
                'p' => 'required|integer',
                'page_size' => 'required|integer',
            ];
            $validate = $cmsCategoryModel->validate()->make($_REQUEST, $rules, [], $map);
            if (false == $validate->passes()) {
                $this->response(null, self::S400_BAD_REQUEST, $validate->messages()->first());
            }
        }
        switch ($model_result['dictionary_value']) {
            case 'page':
                $cmsPostModel = new CmsPostModel();
                $orm = $cmsPostModel->orm()->where(['category_id' => $category_id]);
                $count = $cmsPostModel->getRecordList($orm, '', '', true);
                $result = $cmsPostModel->getRecordList($orm, 0, $count, false);
                $list = [];
                foreach ($result as $key => $value) {
                    $post = $cmsPostModel->getRecordInfoById($value['id']);
                    $list[] = $post;
                }
                $return = [
                    'list' => $list,
                ];
                break;
            case 'article':
                $p = isset($_REQUEST['p']) && !empty($_REQUEST['p']) ? intval($_REQUEST['p']) : 0;
                $page_size = isset($_REQUEST['page_size']) && !empty($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 0;
                $cmsPostModel = new CmsPostModel();
                $orm = $cmsPostModel->orm()->where(['category_id' => $category_id]);
                $count = $cmsPostModel->getRecordList($orm, '', '', true);
                $page = new Page($count, $p, $page_size);
                $result = $cmsPostModel->getRecordList($orm, $page->getOffset(), $page->getPageSize(), false);
                $list = [];
                $cmsCategoryModel = new CmsCategoryModel();
                foreach ($result as $key => $value) {
                    $post = $cmsPostModel->getRecordInfoById($value['id']);
                    $category_result = $cmsCategoryModel->getRecordInfoById($post['category_id']);
                    $post['category_name'] = $category_result['category_name'];
                    $post['category_alias'] = $category_result['category_alias'];
                    $list[] = $post;
                }
                $return = [
                    'count' => $count,
                    'list' => $list,
                ];
                break;
        }

        $this->response($return, self::S200_OK, null, true);
    }

    public function search()
    {
        $cmsPostModel = new CmsPostModel();
        $map = [
            'keyword' => '关键字',
            'p' => '当前页数',
            'page_size' => '每页记录条数',
        ];
        $rules = [
            'keyword' => 'required',
            'p' => 'required|integer',
            'page_size' => 'required|integer',
        ];
        $validate = $cmsPostModel->validate()->make($_REQUEST, $rules, [], $map);
        if (false == $validate->passes()) {
            $this->response(null, self::S400_BAD_REQUEST, $validate->messages()->first());
        }
        $keyword = $_REQUEST['keyword'];
        $p = intval($_REQUEST['p']);
        $page_size = intval($_REQUEST['page_size']);
        $sql = "SELECT  post_id FROM cms_post_extend_attribute WHERE `value` LIKE '%{$keyword}%' UNION SELECT  post_id FROM cms_post_extend_text WHERE `value` LIKE '%{$keyword}%'";
        $post_ids = $cmsPostModel->orm()->raw_query($sql)->find_array();
        $count = count($post_ids);
        $page = new Page($count, $p, $page_size);
        $page_list = array_slice($post_ids, $page->getOffset(), $page_size);
        $list = [];
        $cmsCategoryModel = new CmsCategoryModel();
        foreach ($page_list as $value) {
            $post = $cmsPostModel->getRecordInfoByPostid($value['post_id']);
            $category_result = $cmsCategoryModel->getRecordInfoById($post['category_id']);
            $post['category_name'] = $category_result['category_name'];
            $post['category_alias'] = $category_result['category_alias'];
            $list[] = $post;
        }
        $return = [
            'count' => $count,
            'list' => $list,
        ];
        $this->response($return, self::S200_OK, null, true);
    }

    public function post()
    {
        $cmsPostModel = new CmsPostModel();
        $cmsCategoryModel = new CmsCategoryModel();
        $rules = [
            'post_id' => 'required|numeric'
        ];
        $map = [
            'post_id' => '文档id',
        ];
        $validate = $cmsPostModel->validate()->make($_REQUEST, $rules, [], $map);
        if (false == $validate->passes()) {
            $this->response(null, self::S400_BAD_REQUEST, $validate->messages()->first());
        }
        $post_id = $_REQUEST['post_id'];
        $article = $cmsPostModel->getRecordInfoByPostid($post_id);
        $category_result = $cmsCategoryModel->getRecordInfoById($article['category_id']);
        $article['category_name']=$category_result['category_name'];
        $article['category_alias']=$category_result['category_alias'];
        if (!$article) {
            $this->response(null, self::S404_NOT_FOUND);
        } else {
            $post_id = $article['post_id'];
            $pre_result = $cmsPostModel->getPre($post_id, 'title_alias,id,post_id,title,main_image');
            $next_result = $cmsPostModel->getNext($post_id, 'title_alias,id,post_id,title,main_image');
            $result['article'] = $article;
            $result['pre'] = $pre_result;
            $result['next'] = $next_result;
            $this->response($result, self::S200_OK, null, true);
        }
    }

}