<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/10
 * Time: 20:48
 */

namespace Home\Model;


use Think\Model;

class AddressModel extends Model{
    protected $patchValidate = true;
    protected $_validate = [
        ['name','require','收货人姓名不能为空'],
        ['province_id','require','省份不能为空'],
        ['city_id','require','市级城市不能为空'],
        ['area_id','require','区县不能为空'],
        ['detail_address','require','详细地址不能为空'],
        ['tel','require','手机不能为空'],
    ];

    //添加用户收货地址
    public function addAddress() {
        $userinfo = session('USERINFO');
        if(isset($this->data['is_default'])){
            $this->where(['member_id'=>$userinfo['id']])->setField('is_default',0);
        }
        $this->data['member_id'] = $userinfo['id'];
        return $this->add();
    }
    //获取当前用户的所以
    public function getList() {
        $userinfo = session('USERINFO');
        return $this->where(['member_id'=>$userinfo['id']])->select();
    }
    //设置默认地址
    public function setdefault($id){
        $userinfo = session('USERINFO');
        $this->where(['member_id'=>$userinfo['id']])->setField('is_default',0);
        return $this->where(['id'=>$id])->setField('is_default',1);
    }
    //获取指定的地址信息
    public function getAddressInfo($id,$fields = '*'){
        return $this->field($fields)->where(['id'=>$id])->find();
    }
    //修改地址
    public function saveAddress(){
        $id = I('post.id');
        $userinfo = session('USERINFO');
        if(isset($this->data['is_default'])){
            $this->where(['member_id'=>$userinfo['id']])->setField('is_default',0);
        }else{
            $this->data['is_default'] = 0;
        }
        return $this->where(['id'=>$id])->save();
    }
}