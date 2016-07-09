<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/5
 * Time: 17:08
 */

namespace Home\Controller;


use Think\Controller;

class CaptchaController extends Controller{

    public function captcha(){
        //1.配置
        $setting = ['length'=>4];
        //2.创建对象
        $verify = new \Think\Verify($setting);
        //3.调用entry方法
        $verify->entry();
    }

}