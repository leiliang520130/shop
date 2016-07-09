<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/4
 * Time: 12:45
 */

namespace Admin\Controller;


use Think\Controller;

class CaptchaController extends Controller{
    public function captcha(){
        $option = [
            'length'=>4,
        ];
        $verify = new \Think\Verify($option);
        $verify->entry();
    }
}