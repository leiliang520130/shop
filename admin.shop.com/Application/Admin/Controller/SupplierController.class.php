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
     * @var \Admin\Model\SupplierController
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
        $rows = $this->model->where($cond)->select();
        $this->assign('rows',$rows);
        $this->display();
    }

    /**
     *
     */
    public function add(){
        $this->display();
    }

    public function edit(){

    }

    public function remove(){

    }
}