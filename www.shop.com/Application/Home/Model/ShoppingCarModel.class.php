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

        }else{
            //获取cookie的数据
        }
    }
}