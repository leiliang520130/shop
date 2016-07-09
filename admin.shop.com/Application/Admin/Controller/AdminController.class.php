<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/2
 * Time: 10:56
 */

namespace Admin\Controller;


use Think\Controller;

class AdminController extends Controller{
    /**
     * @var \Admin\Model\AdminModel
     */
    protected $model = null;
    public function _initialize(){
        $this->model = D('Admin');
    }

    public function index(){
        $name = I('get.name');
        $cond = [];
        if ($name) {
            $cond['username'] = [
                'like', '%' . $name . '%'
            ];
        }
        $data = $this->model->getPageResult($cond);
        $this->assign($data);
        $this->display();
    }

    public function add(){
        if(IS_POST){
            if($this->model->create('','register') === false){
                $this->error(get_error($this->model));
            }
            if($this->model->addAdmin() === false){
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
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->saveAdmin($id) === false){
                $this->error(get_error($this->model));
            }
            $this->success('修改成功',U('index'));
        }else{
            //权限显示
            $this->_before_view();
            //显示数据
            $row = $this->model->getAdminInfo($id);
            $this->assign('row',$row);
            $this->display('add');
        }
    }

    public function remove($id){
        if($this->model->deleteAdmin($id) === false){
            $this->error(get_error($this->model));
        }
        $this->success('删除成功',U('index'));
    }

    public function _before_view(){
        $adminRoleModel = D('Role');
        $roles = $adminRoleModel->getList();
        $this->assign('roles',json_encode($roles));
    }

    public function resetpassword($id){
        $p = I('post.resetpassword');
       if(IS_POST){
            if(empty($p)){
                $str = $this->model->saveresetpassword($id);
                echo "<script>alert('修改的密码为:".$str."');location.href='".U('index')."'</script>";
            }else{
                if($this->model->create() === false){
                    $this->error(get_error($this->model));
                }
                if($this->model->saveresetpassword($id) === false){
                    $this->error(get_error($this->model));
                }
                $this->success('修改密码成功',U('index'));
            }

        }else{
           $this->display();
       }
    }

    //登录方法
    public function login(){
        if(IS_POST){
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->login() === false){
                $this->error(get_error($this->model));
            }
            $this->success('登录成功',U('Index/index'));
        }
        $this->display();
    }
    //退出登录
    public function logout(){
        session(null);
        cookie(null);
        $this->success('退出成功',U('login'));
    }
}