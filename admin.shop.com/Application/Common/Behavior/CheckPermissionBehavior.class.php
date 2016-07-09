<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/21
 * Time: 19:00
 */

namespace Common\Behavior;


use Think\Behavior;

class CheckPermissionBehavior extends Behavior
{
    public function run(&$params)
    {
        //dump(cookie('USER_AUTO_LOGIN_TOKEN'));exit;
        $url = MODULE_NAME . "/" . CONTROLLER_NAME . '/' . ACTION_NAME;

        $ignore_setting = C('ACCESS_IGNORE');

        //配置所有用户都可以访问的页面
        $ignore = $ignore_setting['IGNORE'];
        if(in_array($url,$ignore)){
            return;
        }
        //获取用户信息
        $userinfo = session("USERINFO");

        if(isset($userinfo['username']) && $userinfo['username'] == 'admin'){
            return true;
        }
        //如果没有登陆,就自动登陆
        if(!$userinfo){
            $userinfo = D('Admin')->autoLogin();
        }

        //登陆用户可见页面
        $user_ignore = $ignore_setting['USER_IGNORE'];
        //获取权限列表
        $pathes = session('PATHS');
        //允许访问的页面有,角色处获取的权限和忽略列表
        $urls = $pathes;
        if($userinfo){
            //登陆用户可见页面还要额外加上登陆后的忽略列表
            $urls = array_merge($urls,$user_ignore);
        }
        if(!in_array($url, $urls)){

            header('Content-Type: text/html;charset=utf-8');
            redirect(U('Admin/Admin/login'), 3, '无权访问');
        }

    }
}