<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/7
 * Time: 21:12
 */

namespace Home\Model;


use Think\Model;

class GoodsModel extends Model{
    //根据状态查询商品信息
    public function getListByGoodsStatus($goods_status){
        $cond = [
            'status'=>1,
            'is_on_sale'=>1,
            'goods_status & '.$goods_status,
        ];
        return $this->where($cond)->select();
    }

    /**
     * 根据id显示获取商品信息
     * @param $id
     */
    public function getGoodsInfo($id) {
        $row =  $this->field('g.*,b.name as bname,gi.content')->alias('g')->where(['is_on_sale'=>1,'g.status'=>1,'g.id'=>$id])->join('__BRAND__ as b ON g.brand_id = b.id')->join('__GOODS_INTRO__ as gi ON gi.goods_id=g.id')->find();
        $row['galleries'] = M('GoodsGallery')->where(['goods_id'=>$id])->getField('path',true);
        return $row;
    }
}