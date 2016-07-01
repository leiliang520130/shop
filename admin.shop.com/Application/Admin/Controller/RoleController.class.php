<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/1
 * Time: 22:25
 */

namespace Admin\Controller;


use Think\Controller;

class RoleController extends Controller{
    /**
     * @var \Admin\Model\RoleModel
     */
    private $model = null;

    protected function _initialize() {
        $this->model = D('Role');
    }
    public function index(){
        $name = I('get.name');
        $cond = [];
        if ($name) {
            $cond['name'] = [
                'like', '%' . $name . '%'
            ];
        }
        $this->assign($this->model->getPageResult($cond));
        $this->display();
    }

    public function add(){
        if(IS_POST){
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->addRole() === false){
                $this->error(get_error($this->model));
            }
            $this->success('添加成功',U('index'));
        }else{
            $this->_before_view();
            $this->display();
        }


    }

    public function edit($id){
        if(IS_POST){
            if ($this->model->create() === false) {
                $this->error(get_error($this->model));
            }
            if ($this->model->saveRole() === false) {
                $this->error(get_error($this->model));
            }
            $this->success('修改成功', U('index'));
        }else{
            //1.获取角色及其对应的权限
            $row = $this->model->getPermissionInfo($id);
            $this->assign('row',$row);
            $this->_before_view();
            $this->display('add');
        }
    }

    public function remove($id){
        if($this->model->deleteRole($id) === false){
            $this->error(get_error($this->model));
        }
        $this->success('删除成功', U('index'));
    }

    private function _before_view() {
        //获取所有权限
        $permission_model = D('Permission');
        $permissions      = $permission_model->getList();
        //传递
        $this->assign('permissions', json_encode($permissions));
    }

}