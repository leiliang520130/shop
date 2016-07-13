<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/10
 * Time: 20:23
 */

namespace Home\Model;


use Think\Model;

class LocationsModel extends Model{
    public function getListByParentId($parent_id = 0) {
        return $this->where(['parent_id'=>$parent_id])->select();
    }
}