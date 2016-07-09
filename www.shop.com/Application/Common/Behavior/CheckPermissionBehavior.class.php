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
       /* $url = MODULE_NAME . "/" . CONTROLLER_NAME . '/' . ACTION_NAME;
        if($url == 'Home/Member/login' || $url == 'Home/Member/reg' || $url == 'Home/Captcha/captcha'){
            return;
        }*/
        //获取用户信息
        if(!$userinfo = session("USERINFO")){
            $userinfo = D('Member')->autoLogin();
          /*  if(!$userinfo){
                redirect(U('Member/login'), 3, '你还没有登录,请登录');
            }*/
        }

    }
}