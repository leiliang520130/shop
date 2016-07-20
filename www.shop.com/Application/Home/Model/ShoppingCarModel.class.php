<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/8
 * Time: 18:49
 */

namespace Home\Model;


use Think\Model;

class ShoppingCarModel extends Model{
    public function getAmountByGoodsId($goods_id){
        $userinfo = session('USERINFO');
        $cond = [
            'member_id'=>$userinfo['id'],
            'goods_id'=>$goods_id,
        ];
        return $this->where($cond)->getField('amount');
    }

    public function addAmount($goods_id,$amount){
        $userinfo = session('USERINFO');
        $cond = [
            'member_id'=>$userinfo['id'],
            'goods_id'=>$goods_id,
        ];
        return $this->where($cond)->setInc('amount'.$amount);
    }

    public function add2car($goods_id,$amount){
        $userinfo = session('USERINFO');
        $data = [
            'member_id'=>$userinfo['id'],
            'goods_id'=>$goods_id,
            'amount'=>$amount,
        ];
        return $this->add($data);
    }
    //将cookie数据存入数据库
    /**
     * 1.判断cookie是否有数据没有就直接返回
     * 2.先删除数据相同数据
     * 3.添加数据
     */
    public function cookie2db(){
        $userinfo = session('USERINFO');
        $key = C('SHOPPING_CAR_COOKIE_KEY');
        $cookie_car = cookie($key);
        if(!$cookie_car){
            return true;
        }
        $cond = [
            'member_id' => $userinfo['id'],
            'goods_id'=>[
                'in',array_keys($cookie_car),
            ],
        ];
        //执行删除操作
        $this->where($cond)->delete();
        //将数据添加到数据库
        $data = [];
        foreach($cookie_car as $key=>$value){
            $data[] = [
                'goods_id'=>$key,
                'amount'=>$value,
                'member_id'=>$userinfo['id'],
            ];
        }
       return $this->addAll($data);
    }

    //获取购物车的信息展示判断是否登录
    /**
     * 1.1如果登录从数据读取数据
     * 1.2查询商品信息
     * 1.3返回给前端页面展示
     * 2.1如果没有登录获取cookie中发数据
     * 2.2查询goods商品信息
     * 2.3返回给前端页面展示
     */
    public function getShoppingCarList(){
        //判断是否登录
        $userinfo = session('USERINFO');
        //如果登录获取数据库的数据
        if($userinfo){
            $car_list = $this->where(['member_id'=>$userinfo['id']])->getField('goods_id,amount');
        }else{
            //获取cookie的数据
            $car_list = cookie(C('SHOPPING_CAR_COOKIE_KEY'));
        }
        if(!$car_list){
            return [
                'total_price' => '0.00',
                'goods_info_list'=>[],
            ];
        }

        //获取了购物车信息查询商品信息
        $goodsModel = M('Goods');
        $cond = [
            'id'=>['in',array_keys($car_list)],
            'is_on_sale'=>1,
            'status'=>1,
        ];
        $goods_info_list = $goodsModel->where($cond)->getField('id,name,logo,shop_price');
        $total_price = 0;
        //读取用户的积分
        $score = M('Member')->where(['id'=>$userinfo['id']])->getField('score');
        //获取用户的级别
        $cond = [
            'bottom'=>['elt',$score],
            'top'=>['egt',$score],
        ];
        $member_level = M('MemberLevel')->where($cond)->field('id,discount')->find();
        $member_level_id = $member_level['id'];
        $discount = $member_level['discount'];
        //获取用户的会员价
        $member_goods_price_model = M('MemberGoodsPrice');
        foreach($car_list as $goods_id=>$amount){
            //获取当前商品的会员价
            $cond = [
                'goods_id'=>$goods_id,
                'member_level_id'=>$member_level_id,
            ];
            $member_price = $member_goods_price_model->where($cond)->getField('price');
            if($member_price){
                $goods_info_list[$goods_id]['shop_price'] = locate_number_format($member_price);
            }elseif($userinfo){
                $goods_info_list[$goods_id]['shop_price'] = locate_number_format($goods_info_list[$goods_id]['shop_price'] * $discount / 100);
            }else{
                $goods_info_list[$goods_id]['shop_price'] = locate_number_format($goods_info_list[$goods_id]['shop_price']);
            }
            //此时应当将会员价读取出来
            $goods_info_list[$goods_id]['amount'] = $amount;

            $goods_info_list[$goods_id]['sub_total'] = locate_number_format($goods_info_list[$goods_id]['shop_price'] * $amount);
            $total_price += $goods_info_list[$goods_id]['sub_total'];
        }
        $total_price = locate_number_format($total_price);
        return compact('total_price','goods_info_list');
    }

    //删除购物车
    public function clearShoppingCar() {
        $userinfo = session('USERINFO');
        return $this->where(['member_id'=>$userinfo['id']])->delete();
    }
}