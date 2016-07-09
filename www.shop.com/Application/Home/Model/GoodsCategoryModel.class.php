<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/7
 * Time: 18:01
 */

namespace Home\Model;


use Think\Model;

class GoodsCategoryModel extends Model{
    public function getLIst($field = '*'){
        return $this->field($field)->where(['status'=>1])->select();
    }

}