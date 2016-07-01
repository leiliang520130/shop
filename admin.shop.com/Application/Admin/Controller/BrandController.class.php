<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/6/25
 * Time: 0:03
 */

namespace Admin\Controller;


use Think\Controller;

class BrandController extends Controller{
    /**
     * @var \Admin\Model\BrandModel
     */
    private $model = null;
    public function _initialize(){
        $this->model = D('Brand');
    }
    /**
     * 1.显示品牌添加页面
     */
    public function index(){
        $name = I('get.name');
        $cond['status'] = ['egt',0];
        if($name){
            $cond['name'] = ['like','%'.$name.'%'];
        }
        $data = $this->model->getPage($cond);
        $this->assign($data);
        $this->display();
    }

    //品牌添加页面
    public function add(){
        if(IS_POST){
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->addBrand($_FILES['logo']) === false){
                $this->error(get_error($this->model));
            }else{
                $this->success('添加成功',U('index'));
            }
        }else{
            $this->display();
        }
    }

    public function edit($id){
        if(IS_POST){
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->saveBrand($_FILES['logo']) === false){
                $this->error(get_error($this->model));
            }
            $this->success('修改成功',U('index'));
        }else{
            $row = $this->model->find($id);
            $this->assign('row',$row);
            $this->display('add');
        }
    }

    public function remove($id){
        $data = [
            'id'=>$id,
            'status'=>-1,
            'name'=>['exp','concat(name,"_del")'],
        ];
        if($this->model->setField($data) === false){
            $this->error('删除失败');
        }else{
            $this->success('删除成功',U('index'));
        }
    }
}