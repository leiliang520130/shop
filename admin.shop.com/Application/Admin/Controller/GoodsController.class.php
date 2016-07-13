<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/6/29
 * Time: 11:31
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsController extends Controller{
    private $model = null;

    protected function _initialize() {
        $this->model = D('Goods');
    }
    //显示列表
    public function index(){
        $name = I('get.keyword');
        if($name){
            $cond['name'] = ['like','%'.$name.'%'];
        }
        //分类操作
        $goods_category_id = I('get.goods_category_id');
        if($goods_category_id){
            $cond['goods_category_id'] = $goods_category_id;
        }

        //品牌操作
        $brand_id = I('get.brand_id');
        if($brand_id){
            $cond['brand_id'] = $brand_id;
        }
        //精品热销
        $goods_status = I('get.goods_status');
        if($goods_status){
            $cond[] = 'goods_status & '.$goods_status;
        }
        //上架下架
        $is_on_sale = I('get.is_on_sale');
        if($is_on_sale){
            $cond['is_on_sale'] = $is_on_sale;
        }
        //获取供所以分类
        $goodsCategoryModel = D('GoodsCategory');
        $goods_categories = $goodsCategoryModel->getList();
        $this->assign('goods_categories',$goods_categories);
        //显示品牌
        $goodsBrandModel = D('Brand');
        $brands = $goodsBrandModel->getList();
        $this->assign('brands', $brands);
        //显示上架数据
        $goods_statuses = [
            ['id' => 1, 'name' => '精品',],
            ['id' => 2, 'name' => '新品',],
            ['id' => 4, 'name' => '热销',],
        ];
        $this->assign('goods_statuses', $goods_statuses);
        $is_on_sales = [
            ['id' => 1, 'name' => '上架',],
            ['id' => 0, 'name' => '下架',],
        ];
        $this->assign('is_on_sales', $is_on_sales);
        //显示分页数据
        $data = $this->model->getPageResult($cond);
        $this->assign($data);
        $this->display();
    }

    public function add(){
        if (IS_POST) {
            //收集数据
            if($this->model->create()===false){
                $this->error(get_error($this->model));
            }
            if($this->model->addGoods() === false){
                $this->error(get_error($this->model));
            }
            $this->success('添加成功',U('index'));
        } else {
            //显示分类
            $this->_before_view();
            $this->display();
        }
    }

    public function edit($id){
        if (IS_POST) {
            //收集数据
            if($this->model->create()===false){
                $this->error(get_error($this->model));
            }
            if($this->model->saveGoods() === false){
                $this->error(get_error($this->model));
            }
            $this->success('修改成功',U('index'));
        } else {
            //展示数据
            $row = $this->model->getGoodsInfo($id);
            $this->assign('row', $row);
            //获取所有的分类
            $this->_before_view();
            $this->display('add');
        }
    }

    public function remove($id) {
        if($this->model->deleteGoods($id)===false){
            $this->error(get_error($this->model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }

    public function _before_view(){
        $goodsCategoryModel = D('GoodsCategory');
        $goods_categories = $goodsCategoryModel->getList();
        $this->assign('goods_categories', json_encode($goods_categories));
        //显示品牌
        $goodsBrandModel = D('Brand');
        $brands = $goodsBrandModel->getList();
        $this->assign('brands', $brands);
        //显示供货商
        $goodsSupplierModel = D('Supplier');
        $suppliers = $goodsSupplierModel->getList();
        $this->assign('suppliers', $suppliers);
        //准备会员等级列表
        $memberLevelModel = D('MemberLevel');
        $this->assign('member_levels',$memberLevelModel->getList());
    }

    /**
     * 删除图片的方法
     *
     */

    public function removeGallery($id){
        $goodGalleryModel = M('GoodsGallery');
        if($goodGalleryModel->delete($id) === false){
            $this->error('删除失败');
        }else{
            $this->success('删除成功');
        }

    }
}