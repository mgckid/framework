<?php

/**
 * Description of CmsPostModel
 *
 * @date 2016年5月8日 14:11:16
 *
 * @author Administrator
 */

namespace app\model;

use app\logic\BaseLogic;

class CmsPostModel extends BaseModel
{

    public $tableName = 'cms_post';
    public $pk = 'id';


    /**
     * 获取下一篇文章
     * @param $id
     * @param $cateid
     * @param string $field
     * @return mixed
     */
    public function getNext($post_id, $field = '*')
    {
        $post_result = $this->getRecordInfoByPostid($post_id);
        $next_result = $this->orm()
            ->where('category_id', $post_result['category_id'])
            ->where_gt('id', $post_result['id'])
            ->order_by_desc('id')
            ->find_one();
        $result = [];
        if ($next_result) {
            $next_result = $next_result->as_array();
            $result = $this->getRecordInfoById($next_result['id'], $field);
        }
        return $result;
    }

    /**
     * 获取上一篇文章
     * @param $id
     * @param $cateid
     * @param string $field
     * @return mixed
     */
    public function getPre($post_id, $field = '*')
    {
        $post_result = $this->getRecordInfoByPostid($post_id);
        $pre_result = $this->orm()
            ->where('category_id', $post_result['category_id'])
            ->where_lt('id', $post_result['id'])
            ->order_by_asc('id')
            ->find_one();
        $result = [];
        if ($pre_result) {
            $pre_result = $pre_result->as_array();
            $result = $this->getRecordInfoById($pre_result['id'], $field);
        }
        return $result;
    }


    /**改版后****/
    public function getRecordInfoByPostid($post_id, $field = '*')
    {
        $return = [];
        $orm = $this->orm()->where('post_id', $post_id);
        $cms_post_result = $this->getRecordInfo($orm, $field);
        if ($cms_post_result) {
            $return = $this->getRecordInfoById($cms_post_result['id'], $field);
        }
        return $return;
    }

    public function getRecordInfoByTitleAlias($title_alias, $field = '*')
    {
        $return = [];
        $cmsPostExtendAttributeModel = new CmsPostExtendAttributeModel();
        $orm = $cmsPostExtendAttributeModel->orm()->where(['field' => 'title_alias', 'value' => $title_alias]);
        $result = $cmsPostExtendAttributeModel->getRecordInfo($orm);
        if ($result) {
            $return = $this->getRecordInfoByPostid($result['post_id'], $field);
        }
        return $return;
    }

    /**
     * 获取单条记录
     * @access public
     * @author furong
     * @param $id
     * @param string $field
     * @return array|bool
     * @since 2017年7月28日 09:40:34
     * @abstract
     */
    public function getRecordInfoById($id, $field = '*')
    {
        $cms_post_result = parent::getRecordInfoById($id);
        if (!$cms_post_result) {
            return false;
        }
        $logic = new BaseLogic();
        $model_defined = $logic->getModelDefined($cms_post_result['model_id']);

        $cms_post_extend_attribute_select[] = 'post_id';
        $cms_post_extend_text_select[] = 'post_id';
        $select_expr = '';
        foreach ($model_defined as $value) {
            if ($field != '*') {
                $fields = explode(',', $field);
                foreach ($fields as $val) {
                    if ($value['field_value'] == $val) {
                        $select_expr[] = $value['belong_to_table'] . '.' . $value['field_value'];
                    }
                }
            } else {
                $select_expr[] = $value['belong_to_table'] . '.' . $value['field_value'];
            }
            if ($value['belong_to_table'] == 'cms_post_extend_attribute') {
                $cms_post_extend_attribute_select[] = "max( CASE field WHEN '{$value['field_value']}' THEN `value` ELSE '' END ) AS {$value['field_value']}";
            }
            if ($value['belong_to_table'] == 'cms_post_extend_text') {
                $cms_post_extend_text_select[] = "max( CASE field WHEN '{$value['field_value']}' THEN `value` ELSE '' END ) AS {$value['field_value']}";
            }
        }
        #cms_post_extend_attribute
        {
            $cms_post_extend_attribute_select = implode(',', $cms_post_extend_attribute_select);
            $cms_post_extend_attribute_raw_join = "left join (SELECT {$cms_post_extend_attribute_select} FROM cms_post_extend_attribute  GROUP BY post_id )";
        }
        #cms_post_extend_text
        {
            $cms_post_extend_text_select = implode(',', $cms_post_extend_text_select);
            $cms_post_extend_text_raw_join = "left join (SELECT {$cms_post_extend_text_select} FROM cms_post_extend_text  GROUP BY post_id )";
        }

        $orm = $this->orm()->raw_join($cms_post_extend_attribute_raw_join, ['cms_post.post_id', '=', 'cms_post_extend_attribute.post_id'], 'cms_post_extend_attribute')
            ->raw_join($cms_post_extend_text_raw_join, ['cms_post.post_id', '=', 'cms_post_extend_text.post_id'], 'cms_post_extend_text')
            ->where('cms_post.id', $id);
        $result = $this->getRecordInfo($orm, implode(',', $select_expr));
        return $result;
    }

    /**
     * 添加文档
     * @access public
     * @author furong
     * @param $request_data
     * @return bool
     * @since 2017年8月2日 15:48:44
     * @abstract
     */
    public function addRecord($request_data)
    {
        #获取模型定义
        $baseLogic = new BaseLogic();
        $model_defined = $baseLogic->getModelDefined($request_data['model_id']);
        $cms_post_data = [];
        $extend_data = [];
        foreach ($model_defined as $value) {
            switch ($value['belong_to_table']) {
                case 'cms_post':
                    if (isset($request_data[$value['field_value']])) {
                        $cms_post_data[$value['field_value']] = $request_data[$value['field_value']];
                    }
                    break;
                default:
                    if (isset($request_data[$value['field_value']])) {
                        $extend_data[] = [
                            'table_name' => $value['belong_to_table'],
                            'post_id' => $request_data['post_id'],
                            'field' => $value['field_value'],
                            'value' => $request_data[$value['field_value']],
                        ];
                    }
            }
        }
        try {
            $this->beginTransaction();
            $cms_post_result = parent::addRecord($cms_post_data);
            if (!$cms_post_result) {
                throw new \Exception('文档主记录添加失败');
            }
            if ($extend_data) {
                foreach ($extend_data as $value) {
                    $result = $this->addCmsPostExtendData($value['table_name'], $value['post_id'], $value['field'], $value['value']);
                    if (!$result) {
                        throw new \Exception('文档扩展记录添加失败');
                    }
                }
            }
            $this->commit();
            $return = true;
        } catch (\Exception $ex) {
            $this->rollBack();
            $this->setMessage($ex->getMessage());
            $return = false;
        }
        return $return;
    }

    public function addCmsPostExtendData($table_name, $post_id, $field, $value)
    {
        $orm = $this->orm()->for_table($table_name)->use_id_column('id');
        $data = [
            'post_id' => $post_id,
            'field' => $field,
            'value' => $value
        ];
        $result = $orm->where('post_id', $post_id)
            ->where('field', $field)
            ->find_one();
        if ($result) {
            $return = $result->set($data)->save();
        } else {
            $return = $orm->create($data)->save();
        }
        if (!$return) {
            throw new \Exception('文档扩展记录添加失败');
        }
        return $return;
    }




    /**
     * 获取文章列表
     * @param type $condition 条件
     * @param type $offset 偏移量
     * @param type $limit 获取条数
     * @param type $forCount 统计
     * @param type $field 字段
     * @return type
     */
    public function getPostList($orm = '', $offset, $limit, $forCount = false, $order_by = 'a.id', $sort = 'desc', $field = 'a.*,c.category_name')
    {
        $orm = $this->getOrm($orm)->where('a.deleted', 0)
            ->table_alias('a')
            ->select_expr($field)
            ->left_outer_join('cms_category', array('a.category_id', '=', 'c.id'), 'c');
        #排序
        if ($sort == 'desc') {
            $orm = $orm->order_by_desc($order_by);
        } elseif ($sort == 'asc') {
            $orm = $orm->order_by_asc($order_by);
        }
        if ($forCount) {
            $result = $orm->count();
        } else {
            $result = $orm
                ->limit($limit)
                ->offset($offset)
                ->order_by_desc('id')
                ->find_array();
        }
        return $result;
    }

    /**
     * 获取文档扩展属性
     * @param $post_id
     * @return array
     */
    public function getPostExtendAttrbute($post_id)
    {
        $result = $this->for_table('cms_post_extend_attribute')
            ->use_id_column('id')
            ->select_expr('field,value')
            ->where('post_id', $post_id)
            ->find_array();
        if ($result) {
            $field = array_column($result, 'field');
            $value = array_column($result, 'value');
            $result = array_combine($field, $value);
        }
        return $result;
    }

    /**
     * 获取记录列表
     * @access public
     * @author furong
     * @param $orm
     * @param string $offset
     * @param string $limit
     * @param bool $for_count
     * @param string $field
     * @return void
     * @since 2017年7月6日 17:14:49
     * @abstract
     */
    public function getRecordList1($orm = '', $offset = '', $limit = '', $for_count = false, $order_by_id_desc = true, $field = '*')
    {
        $orm = $this->getOrm($orm)->where_equal('deleted', 0);
        if ($for_count) {
            $result = $orm->count();
        } else {
            $cms_post_extend_attribute_select[] = 'post_id';
            $cms_post_extend_text_select[] = 'post_id';
            $select_expr = '';
            foreach ($field as $value) {
                $select_expr[] = $value['belong_to_table'] . '.' . $value['field_value'];
                if ($value['belong_to_table'] == 'cms_post_extend_attribute') {
                    $cms_post_extend_attribute_select[] = "max( CASE field WHEN '{$value['field_value']}' THEN `value` ELSE '' END ) AS {$value['field_value']}";
                }
                if ($value['belong_to_table'] == 'cms_post_extend_text') {
                    $cms_post_extend_text_select[] = "max( CASE field WHEN '{$value['field_value']}' THEN `value` ELSE '' END ) AS {$value['field_value']}";
                }


            }
            #cms_post_extend_attribute
            {
                $cms_post_extend_attribute_select = implode(',', $cms_post_extend_attribute_select);
                $cms_post_extend_attribute_raw_join = "left join (SELECT {$cms_post_extend_attribute_select} FROM cms_post_extend_attribute  GROUP BY post_id )";
            }
            #cms_post_extend_text
            {
                $cms_post_extend_text_select = implode(',', $cms_post_extend_text_select);
                $cms_post_extend_text_raw_join = "left join (SELECT {$cms_post_extend_text_select} FROM cms_post_extend_text  GROUP BY post_id )";
            }
            $model = $orm->raw_join($cms_post_extend_attribute_raw_join, ['cms_post.post_id', '=', 'cms_post_extend_attribute.post_id'], 'cms_post_extend_attribute')
                ->raw_join($cms_post_extend_text_raw_join, ['cms_post.post_id', '=', 'cms_post_extend_text.post_id'], 'cms_post_extend_text')
                ->offset($offset)
                ->limit($limit)
                ->select_expr('*');
            if ($order_by_id_desc) {
                $model = $model->order_by_desc($this->pk);
            } else {
                $model = $model->order_by_asc($this->pk);
            }
            $result = $model->find_array();
        }
        return $result;
    }


}
