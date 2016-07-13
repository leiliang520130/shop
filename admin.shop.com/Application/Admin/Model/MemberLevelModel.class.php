<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/12
 * Time: 22:32
 */

namespace Admin\Model;


use Think\Model;

class MemberLevelModel extends Model{
    //获取所有的会员等级
    public function getList(){
     return $this->where(['status'=>1])->order('sort')->select();
    }
}