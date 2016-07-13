<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/12
 * Time: 21:14
 */

namespace Home\Model;


use Think\Model;

class OrderInfoModel extends Model{
    /**
     * 1.创建订单
     * 2.保存基本信息
     * 3·保存订单详情
     * 4.保存发票信息
     * 5·清空购物车
     */
    public function addOrder(){
        //开启事务的支持
        $this->startTrans();
        //收货人信息
        $addressModel = D('Address');
        $addressInfo = $addressModel->getAddressInfo(I('post.address_id'),'province_name,city_name,area_name,tel,name,detail_address,member_id');
        $this->data    = array_merge($this->data, $addressInfo);
        //获取配送方式
        $deliveryModel = D('Delivery');
        $deliveryInfo = $deliveryModel->getDeliveryInfo(I('post.delivery_id'), 'name as delivery_name,price as
delivery_price');
        $this->data     = array_merge($this->data, $deliveryInfo);
        //获取支付方式
        $paymentModel = D('Payment');
        $paymentInfo  = $paymentModel->getPaymentInfo(I('post.pay_type_id'), 'name as pay_type_name');
        $this->data    = array_merge($this->data, $paymentInfo);
        //获取订单金额
        $shoppingCarModel = D('ShoppingCar');
        $cart_info = $shoppingCarModel->getShoppingCarList();
        $this->data['price'] = $cart_info['total_price'];
        $this->data['status']=1;
        if(($order_id=$this->add()) === false){
            $this->error = '保存订单基本信息失败';
            $this->rollback();
            return false;
        }

        //保存订单详情
        $data = [];
        foreach ($cart_info['goods_info_list'] as $goods) {
            $data[] = [
                'order_info_id' => $order_id,
                'goods_id' => $goods['id'],
                'goods_name' => $goods['name'],
                'logo' => $goods['logo'],
                'price' => $goods['shop_price'],
                'amount' => $goods['amount'],
                'total_price' => $goods['sub_total'],
            ];
        }
        $orderInfoItemModel = M('OrderInfoItem');
        if($orderInfoItemModel->addAll($data) === false){
            $this->error = '保存订单详细信息失败';
            $this->rollback();
            return false;
        }
        //保存发票
        //判断个人还是公司
        $receipt_type = I('post.receipt_type');
        if ($receipt_type == 1) {
            $receipt_title = $addressInfo['name'];
        } else {
            $receipt_title = I('post.company_name');
        }
        //发票内容
        $receipt_content_type = I('post.receipt_content_type');
        //实际内容拼接
        $receipt_content = '';
        switch ($receipt_content_type) {
            case 1:
                $tmp = [];
                foreach ($cart_info['goods_info_list'] as $goods) {
                    $tmp[] = $goods['name'] . "\t" . $goods['shop_price'] . '×' . $goods['amount'] . "\t" . $goods['sub_total'];
                }
                $receipt_content = implode("\r\n", $tmp);
                break;
            case 2:
                $receipt_content .= '办公用品';
                break;
            case 3:
                $receipt_content .= '体育休闲';
                break;
            case 4:
                $receipt_content .= '耗材';
                break;
        }
        $content = $receipt_title . "\r\n" . $receipt_content . "\r\n总计：" . $cart_info['total_price'];
        $data    = [
            'name' => $receipt_title,
            'content' => $content,
            'price' => $cart_info['total_price'],
            'inputtime' => NOW_TIME,
            'member_id' => $addressInfo['member_id'],
            'order_info_id' => $order_id,
        ];
        $invoiceModel = M('Invoice');
        if ($invoiceModel->add($data) === false) {
            $this->error = '保存发票失败';
            $this->rollback();
            return false;
        }
        //清空购物车
        if($shoppingCarModel->clearShoppingCar()===false){
            $this->error = '清空购物车失败';
            $this->rollback();
            return false;
        }

        $this->commit();
        return true;

    }
}