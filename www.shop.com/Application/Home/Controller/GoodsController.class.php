<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/8
 * Time: 16:41
 */

namespace Home\Controller;


use Think\Controller;

class GoodsController extends Controller{
    //将点击次数放入数据库
    public function clickTimes($id){
        $goodsClickModel = M('GoodsClick');
        $num = $goodsClickModel->getFieldByGoodsId($id,'click_times');
        if(!$num){
            $num = 1;
            $data = [
                'goods_id'=>$id,
                'click_times'=>$num,
            ];
            $goodsClickModel->add($data);
        }else{
            ++$num;
            $data = [
                'goods_id'=>$id,
                'click_times'=>$num,
            ];
            $goodsClickModel->save($data);
        }

        $this->ajaxReturn($num);
    }
    //将点击次数放入redis中
    public function getClickTimes($id){
        $redis = get_redis();
        $key = 'goods_clicks';
        $this->ajaxReturn($redis->zIncrBy($key,1,$id));
    }

    //将redis中的数据存入数据库持久保存
    public function syncGoodsClicks(){
        $redis = get_redis();
        $key = 'goods_clicks';
        $goods_clicks = $redis->zRange($key,0,-1,true);

        //一次插入500-1000条分段
//        $tmp = array_chunk($goods_clicks,1000,true);//遍历里面的第一维,然后重复使用下面的代码

        //判断redis里面是否有数据
        if(empty($goods_clicks)){
            return true;
        }
        //将redis的数据存入数据之前先删除相关数据
        $goodsClickModel = M('GoodsClick');
        $goods_ids = array_keys($goods_clicks);
        $goodsClickModel->where(['goods_id'=>['in',$goods_ids]])->delete();
        //删除之后添加数据
        //1.先构建数据
        $data = [];
        foreach($goods_clicks as $key=>$value){
            $data[] = [
                'goods_id'=>$key,
                'click_times'=>$value,
            ];
        }
       // echo '<script>window.close()</script>';//关闭浏览器
        return $goodsClickModel->addAll($data);


    }

}