<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/12
 * Time: 21:12
 */

namespace Home\Controller;


use Think\Controller;

class OrderInfoController extends Controller{
    /**
     * @var \Home\Model\OrderInfoModel
     */
    private $model = null;
    protected function _initialize(){
        $this->model = D('OrderInfo');
    }

    /**
     * 1.接收数据
     * 2·创建订单
     * 3.提示跳转
     */
    public function add() {
        if(IS_POST){
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->addOrder() === false){
                $this->error(get_error($this->model));
            }
            $this->success('创建订单成功',U('Cart/flow3'));
        }else{
            $this->error('拒绝访问');
        }
    }
}