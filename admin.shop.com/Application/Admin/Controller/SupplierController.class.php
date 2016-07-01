<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/6/24
 * Time: 17:15
 */

namespace Admin\Controller;


use Think\Controller;

class SupplierController extends Controller{
    /**
     * @var \Admin\Model\SupplierModel
     */
    private $model = null;
    public function _initialize(){
        $this->model = D('Supplier');
    }

    /**
     * 1.查询数据
     * 2.显示数据
     */
    public function index(){
        //var_dump($this->model);exit;
        $name = I('get.name');
        $cond['status'] = ['egt',0];
        if($name){
            $cond['name'] = ['like','%'.$name.'%'];
        }
        $data = $this->model->getPage($cond);
        $this->assign($data);
        $this->display();
    }

    /**
     *
     */
    public function add(){
        if(IS_POST){
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->add() === false){
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
                $this->error('修改失败');
            }
            if($this->model->save() === false){
                $this->error('修改失败');
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