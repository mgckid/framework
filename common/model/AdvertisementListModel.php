<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/3/2
 * Time: 20:19
 */

namespace app\model;


class AdvertisementListModel extends BaseModel
{
    public $tableName = 'advertisement_list';
    public $pk = 'id';

    public function getAdvertisementList(array $where = [], $offset, $limit, $forCount, $field = 'al.*,ap.position_name')
    {
        $orm = $this->orm()
            ->table_alias('al')
            ->left_outer_join('advertisement_position', array('al.position_id', '=', 'ap.id'), 'ap');
        if ($where) {
            foreach ($where as $key => $value) {
                $orm = call_user_func_array(array($orm, $key), $value);
            }
        }
        if ($forCount) {
            $result = $orm->count();
        } else {
            $result = $orm->offset($offset)
                ->select_expr($field)
                ->limit($limit)
                ->order_by_asc('ap.id')
                ->order_by_asc('al.id')
                ->find_array();
        }
        return $result;
    }
}