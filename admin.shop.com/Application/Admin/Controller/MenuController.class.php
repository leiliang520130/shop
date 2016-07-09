<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/3
 * Time: 20:51
 */

namespace Admin\Controller;


use Think\Controller;

class MenuController extends Controller{
    /**
     * @var \Admin\Model\MenuModel
     */
    private $model = null;

    protected function _initialize() {
        $this->model = D('Menu');
    }

    public function index(){
        $this->assign('rows',$this->model->getList());
        $this->display();
    }

    public function add(){
        if (IS_POST) {
            //收集数据
            if ($this->model->create() === false) {
                $this->error(get_error($this->model));
            }
            if ($this->model->addMenu() === false) {
                $this->error(get_error($this->model));
            }
            $this->success('添加成功', U('index'));
        } else {
            $this->_before_view();
            $this->display();
        }
    }

    public function edit($id){
        if (IS_POST) {
            if ($this->model->create() === false) {
                $this->error(get_error($this->model));
            }
            if ($this->model->saveMenu() === false) {
                $this->error(get_error($this->model));
            }
            $this->success('修改成功', U('index'));
        } else {
            //展示数据
            $row = $this->model->getMenuInfo($id);
            $this->assign('row', $row);
            $this->_before_view();
            $this->display('add');
        }
    }

    public function remove($id){
        if ($this->model->deleteMenu($id) === false) {
            $this->error(get_error($this->model));
        } else {
            $this->success('删除成功', U('index'));
        }
    }

    private function _before_view() {
        $menus = $this->model->getList();
        array_unshift($menus, ['id' => 0, 'name' => '顶级菜单', 'parent_id' => 0]);
        $menus = json_encode($menus);
        $this->assign('menus', $menus);
        //获取权限列表
        $permissionModel = D('Permission');
        $permissions = $permissionModel->getList();
        $permissions = json_encode($permissions);
        $this->assign('permissions', $permissions);
    }

}