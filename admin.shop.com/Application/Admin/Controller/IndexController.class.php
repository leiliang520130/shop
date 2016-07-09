<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display();
    }
    public function top(){
        $userinfo = session('USERINFO');
        $this->assign('userinfo',$userinfo);
        $this->display();
    }

    public function menu(){
        $menuModel = D('Menu');
        $menus = $menuModel->getMenuList();
        $this->assign('menus',$menus);
        //显示id
        $userinfo = session('USERINFO');
        $id = $userinfo['id'];
        $this->assign('id',$id);
        $this->display();
    }

    public function main(){
        $this->display();
    }
}