<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/13
 * Time: 22:19
 */

namespace Admin\Model;


use Think\Model;

class OrderInfoModel extends Model{
    public $statuses = [
        0=>'已取消',
        1=>'待支付',
        2=>'待发货',
        3=>'待收货',
        4=>'已完成',
    ];
    public function getList(){
        return $this->order('inputtime desc')->select();
    }
}