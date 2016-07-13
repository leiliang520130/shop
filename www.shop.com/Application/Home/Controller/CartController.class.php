<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/8
 * Time: 18:29
 */

namespace Home\Controller;


use Think\Controller;

class CartController extends Controller{
    protected $model = null;
    protected function _initialize(){
        $this->model = D('ShoppingCar');
    }
    /**
     * 加入购物车的方法类
     * 未登录存入cookie登录存入数据库
     * 1·判断是否登录
     * 1.1如果没有登录则检查cookie
     * 1·2判断cookie是否存在记录，
     * 1·3存在则保存，不存在则添加
     * 2.1如果登录存入数据库
     * 2·2存在数据则保存
     * 2·3不存在则添加
     * @param $id
     * @param $amount
     */
    public function add2car($id,$amount){
        $userinfo = session('USERINFO');
        if(!$userinfo){
            //未登录状态
            $key = C('SHOPPING_CAR_COOKIE_KEY');
            $car_list = cookie($key);
            if(isset($car_list[$id])){
                $car_list[$id] += $amount;
            }else{
                $car_list[$id] = $amount;
            }
            cookie($key,$car_list,60*60*24*7);//保存一周
        }else{
            //已登录
            $db_amount = $this->model->getAmountByGoodsId($id);
            if($db_amount){
                $this->model->addAmount($id,$amount);
            }else{
                $this->model->add2car($id,$amount);
            }
        }
        $this->success('添加成功',U('flow1'));
    }
    //显示购物车
    public function flow1() {
        $car_list = $this->model->getShoppingCarList();
        $this->assign($car_list);
        $this->display();
    }

    //填写订单信息
    public function flow2(){
        $userinfo = session('USERINFO');
        if(!$userinfo){
            cookie('__FORWARD__',__SELF__);//记录登录状态页面
            $this->error('你没有登录，请登录!',U('Member/login'));
        }else{
            //1.获取收货地址
            $addressModel = D('Address');
            $this->assign('addresses',$addressModel->getList());
            //2·获取配送方式
            $deliveryModel = D('Delivery');
            $this->assign('deliveries',$deliveryModel->getList());
            //3·获取支付方式
            $paymentModel = D('Payment');
            $this->assign('payments',$paymentModel->getList());
            //4·获取购物车数据
            $car_list = $this->model->getShoppingCarList();
            $this->assign($car_list);
            $this->display();
        }
    }
}