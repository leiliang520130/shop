<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/11
 * Time: 18:42
 */

namespace Home\Model;


use Think\Model;

class DeliveryModel extends Model{

    public function getList(){
        return $this->where(['status'=>1])->order()->select();
    }
    //获取指定配送方式
    public function getDeliveryInfo($id,$field = '*') {
        $cond = [
            'id'=>$id,
        ];
        return $this->field($field)->where($cond)->find();
    }
}