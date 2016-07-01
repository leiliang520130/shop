<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/6/27
 * Time: 19:16
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsCategoryController extends Controller{
    private $model = null;

    protected function _initialize() {
        $this->model = D('GoodsCategory');
    }
    //显示列表
    public function index(){
        $this->assign('rows',$this->model->getList());
        $this->display();
    }

    public function add(){
        if (IS_POST) {
            //收集数据
            if($this->model->create()===false){
                $this->error(get_error($this->model));
            }
            if($this->model->addCategory() === false){
                $this->error(get_error($this->model));
            }
            $this->success('添加成功',U('index'));
        } else {
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
            if($this->model->saveCategory() === false){
                $this->error(get_error($this->model));
            }
            $this->success('修改成功',U('index'));
        } else {
            //展示数据
            $row = $this->model->find($id);
            $this->assign('row', $row);
            //获取所有的分类
            $this->_before_view();
            $this->display('add');
        }
    }

    public function remove($id) {
        if($this->model->deleteCategory($id)===false){
            $this->error(get_error($this->model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }

    public function _before_view(){
        $goods_categories = $this->model->getList();
        array_unshift($goods_categories,['id'=>0,'name'=>'顶级分类','parent_id'=>0]);
        $goods_categories = json_encode($goods_categories);
        $this->assign('goods_categories', $goods_categories);
    }
}