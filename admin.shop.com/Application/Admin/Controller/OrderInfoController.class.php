<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/13
 * Time: 22:14
 */

namespace Admin\Controller;


use Think\Controller;

class OrderInfoController extends Controller{
    //显示所有订单列表

    public function index(){
        $orderInfoModel = D('OrderInfo');
        $rows = $orderInfoModel->getList();
        $this->assign('rows',$rows);
        $this->assign('statuses',$orderInfoModel->statuses);
        $this->display();

    }
    public function send($id) {
        $order_info_model = D('OrderInfo');
        if ($order_info_model->where(['id' => $id])->setField('status', 3) === false) {
            $this->error(get_error($order_info_model));
        } else {
            $this->success('发货成功', U('index'));
        }
       /* if(IS_POST){
            $id = I('post.id');

        }else{
            $this->assign('id',$id);
            $this->display();
        }*/
    }

    public function clearTimeOutOrder() {
        while(true){

            M()->startTrans();
            //获取超时订单
            $orderInfoModel = D('OrderInfo');
            $order_ids = $orderInfoModel->where(['intputtime' => ['lt', NOW_TIME - 900], 'status' => 1])->getField('id', true);
            if (!$order_ids) {
                return true;
            }
            //修改这些订单的状态
            $orderInfoModel->where(['id' => ['in', $order_ids]])->setField('status', 0);
            //恢复库存
            $orderInfoItemModel = M('OrderInfoItem');
            $goods_list            = $orderInfoItemModel->where(['id' => ['in', $order_ids]])->getField('id,goods_id,amount');
            //遍历每个商品,释放库存
            $goods_model = M('Goods');
            $data = [];
            foreach ($goods_list as $goods) {
                if (isset($data[$goods['goods_id']])) {

                    $data[$goods['goods_id']] += $goods['amount'];
                } else {
                    $data[$goods['goods_id']] = $goods['amount'];
                }
            }
            foreach ($data as $goods_id => $amount) {
                $goods_model->where(['id'=>$goods_id])->setInc('stock',$amount);
            }
            M()->commit();
        }
    }

}