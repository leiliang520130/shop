<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/1
 * Time: 18:14
 */

namespace Admin\Controller;


use Think\Controller;

class PermissionController extends Controller{
    /**
     * @var \Admin\Model\PermissionModel
     */
    private $model = null;
    protected function _initialize(){
        $this->model = D('Permission');
    }

    public function index(){
        $rows = $this->model->getList();
        $this->assign('rows',$rows);
        $this->display();
    }

    public function add(){
        if(IS_POST){
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->addPermission() === false){
                $this->error(get_error($this->model));
            }
            $this->success('添加成功',U('index'));
        }else{
            $permissions = $this->model->getList();
            array_unshift($permissions, ['id' => 0, 'name' => '顶级权限', 'parent_id' => null]);
            $this->assign('permissions', json_encode($permissions));
            $this->display();
        }

    }

    public function edit($id){
        if(IS_POST){
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->savePermission($id) === false){
                $this->error(get_error($this->model));
            }
            $this->success('添加成功',U('index'));
        }else{
            $row = $this->model->find($id);
            $this->assign('row',$row);
            $permissions = $this->model->getList();
            array_unshift($permissions, ['id' => 0, 'name' => '顶级权限', 'parent_id' => null]);
            $this->assign('permissions', json_encode($permissions));
            $this->display('add');
        }

    }

    public function remove($id){
        if($this->model->deletePermission($id) === false){
            $this->error(get_error($this->model));
        }
        $this->success('删除成功',U('index'));
    }

}