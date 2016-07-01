<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/6/29
 * Time: 11:32
 */

namespace Admin\Model;


use Think\Model;

class GoodsModel extends Model{
    //批量验证
    protected $patchValidate = true;
    //自动验证
    protected $_validate     = [
        ['name', 'require', '商品名称不能为空'],
        ['sn', '', '货号已存在', self::VALUE_VALIDATE],
        ['goods_category_id', 'require', '商品分类不能为空'],
        ['brand_id', 'require', '品牌不能为空'],
        ['supplier_id', 'require', '供货商不能为空'],
        ['market_price', 'require', '市场价不能为空'],
        ['market_price', 'currency', '市场价不合法'],
        ['shop_price', 'require', '售价不能为空'],
        ['shop_price', 'currency', '售价不合法'],
        ['stock', 'require', '库存不能为空'],
    ];
    //自动完成
    protected $_auto = [
        ['sn','createSn',self::MODEL_INSERT,'callback'],
        ['goods_status','array_sum',self::MODEL_INSERT,'function'],
        ['inputtime',NOW_TIME,self::MODEL_INSERT],
    ];

    protected function createSn($sn){
        $this->startTrans();
        //提交了就直接返回
        if($sn){
            return $sn;
        }
        $date = date(Ymd);
        //查询数量是否存在进行增加
        $goodsNumModel = M('GoodsNum');
        $num = $goodsNumModel->getFieldByDate($date,'num');
        if($num){
            ++$num;
            $data = [
                'date'=>$date,
                'num' =>$num,
            ];
        $flag =  $goodsNumModel->select($data);
        }else{
            $num=1;
            $data = [
                'date'=>$date,
                'num' =>$num,
            ];
        $flag =  $goodsNumModel->add($data);
        }
        if($flag === false){
            $this->rollback();
            return false;
        }
        $sn = 'SN'.$date.str_pad($num,5,0,STR_PAD_LEFT);
        return $sn;

    }
    /**
     * 添加商品的方法
     * @return bool
     */
    public function addGoods(){
        //添加商品
        $goods_id = $this->add();
        if($goods_id === false){
            $this->rollback();
            return false;
        }
        //添加商品详细信息
        $data = [
            'goods_id'=>$goods_id,
            'content'=>I('post.content','',false),
        ];
        $goodsIntroModel = M('GoodsIntro');
        if($goodsIntroModel->add($data) === false){
            $this->rollback();
            return false;
        }

        //添加图片
        $goodsGalleryModel = M('GoodsGallery');
        $pathes = I('post.path');
        $data = [];
        foreach($pathes as $path){
            $data[] = [
                'goods_id'=>$goods_id,
                'path'=>$path,
            ];
        }
        if($data && ($goodsGalleryModel->addAll($data)===false)){
            $this->rollback();
            return false;
        }

        $this->commit();
        return true;
    }

    //首页显示方法
    public function getPageResult(array $cond = []) {
        $cond = array_merge(['status' => 1], $cond);
        //1.获取总条数
        $count = $this->where($cond)->count();
        //2.获取分页代码
        $page_setting = C('PAGE_SETTING');
        $page = new \Think\Page($count, $page_setting['PAGE_SIZE']);
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        $page_html = $page->show();
        //3.获取分页数据
        $rows = $this->where($cond)->page(I('get.p', 1), $page_setting['PAGE_SIZE'])->select();
        foreach ($rows as $key => $value) {
            $value['is_best'] = $value['goods_status'] & 1 ? true : false;
            $value['is_new']  = $value['goods_status'] & 2 ? true : false;
            $value['is_hot']  = $value['goods_status'] & 4 ? true : false;
            $rows[$key] = $value;
        }
        return compact('rows', 'page_html');
    }

    public function getGoodsInfo($id){
        //获取商品的基本信息
        $row = $this->find($id);
        //前端需要转换为json对象
        $tmp = [];
        if($row['goods_status']&1){
            $tmp[] = 1;
        }
        if($row['goods_status']&2){
            $tmp[] = 2;
        }
        if($row['goods_status']&4){
            $tmp[] = 4;
        }
        $row['goods_status'] = json_encode($tmp);
        unset($tmp);
        //获取商品的详细描述
        $goodsIntroModel = M('GoodsIntro');
        $row['content'] = $goodsIntroModel->getFieldByGoodsId($id,'content');
        //获取商品的相册
        $goodsGalleryModel = M('GoodsGallery');
        $row['galleries']=$goodsGalleryModel->getFieldByGoodsId($id,'id,path');
        return $row;
    }

    //修改商品的方法
    public function saveGoods(){
        $requestData = $this->data;
        $this->startTrans();
        if($this->data['goods_status'] === null){
            $this->data['goods_status'] = 0;
        }else{
            $this->data['goods_status'] = array_sum($this->data['goods_status']);
        }
        if($this->save() === false){
            $this->rollback();
            return false;
        }
        //添加商品详细信息
        $data = [
            'goods_id'=>$requestData['id'],
            'content'=>I('post.content','',false),
        ];
        $goodsIntroModel = M('GoodsIntro');
        if($goodsIntroModel->save($data) === false){
            $this->rollback();
            return false;
        }

        //添加图片
        $goodsGalleryModel = M('GoodsGallery');
        $paths = I('post.path');
        $data = [];
        foreach($paths as $path){
            $data[] = [
                'goods_id'=>$requestData['id'],
                'path'=>$path,
            ];
        }
        if($data && ($goodsGalleryModel->addAll($data)===false)){
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    //创建一个删除商品的方法
    public function deleteGoods($id){
        $data = [
            'id'=>$id,
            'status'=>0,
        ];
        return $this->setField($data);

    }
}