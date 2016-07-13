<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/11
 * Time: 18:51
 */

namespace Home\Model;


use Think\Model;

class PaymentModel extends Model{
    public function getList() {
        return $this->where(['status'=>1])->order('sort')->select();
    }

    //获取指定商品
    public function getPaymentInfo($id,$field = '*') {
        $cond = [
            'id'=>$id,
        ];
        return $this->field($field)->where($cond)->find();
    }
}