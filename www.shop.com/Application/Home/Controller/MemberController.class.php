<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/5
 * Time: 16:32
 */

namespace Home\Controller;


use Think\Controller;

class MemberController extends Controller{
    /**
     * @var \Home\Model\MemberModel
     */
    private $model = null;
    protected function _initialize(){
        $this->model = D('Member');
    }

    public function reg(){
        if(IS_POST){
            if($this->model->create('','reg') === false){
                $this->error(get_error($this->model));
            }
            if($this->model->addMember() === false){
                $this->error(get_error($this->model));
            }
            $this->success('注册成功',U('Index/index'));
        }else{
            $this->display();
        }
    }

    public function active($email,$register_token){
        $cond = [
            'emaile'=>$email,
            'register'=>$register_token,
            'status'=>0,
        ];
        if($this->model->where($cond)->count()){
            $this->model->where($cond)->setField('status',1);
            $this->success('激活成功',U('Index/index'));
        }else{
            $this->success('激活失败',U('Index/index'));
        }
    }

    //账号是否存在验证
    public function checkByParam(){
        $cond = I('get.');
        if($this->model->where($cond)->count()){
            $this->ajaxReturn(false);
        }else{
            $this->ajaxReturn(true);
        }
    }

    public function login(){
        if(IS_POST){
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->login() === false){
                $this->error(get_error($this->model));
            }
            //记录登录状态页面
            $url = cookie('__FORWARD__');
            cookie('__FORWARD__',null);
            if(!$url){
                $url = U('Index/index');
            }
            $this->success('登录成功',$url);
        }else{
            $this->display();
        }
    }

    public function logout() {
        session('USERINFO',null);
        cookie(null);
        $this->success('退出成功',U('Index/index'));
    }

    //显示用户名
    public function userinfo(){
        $userinfo = session('USERINFO');
        if($userinfo){
            $this->ajaxReturn($userinfo['username']);
        }else{
            $this->ajaxReturn(false);
        }
    }
}