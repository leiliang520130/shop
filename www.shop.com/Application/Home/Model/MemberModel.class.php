<?php

/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/5
 * Time: 17:22
 */
namespace Home\Model;
use Think\Model;

class MemberModel extends Model{
    protected $patchValidate = true;
    protected $_validate = [
        ['username','require','用户名不能为空'],
        ['username','','用户名已存在',self::EXISTS_VALIDATE,'unique','reg'],
        ['password','require','密码不能为空'],
        ['password','6,16','密码必须6-16位',self::EXISTS_VALIDATE,'length'],
        ['repassword','password','两次密码不一致',self::EXISTS_VALIDATE,'confirm'],
        ['email','require','邮箱不能为空'],
        ['email','email','邮箱不合法'],
        ['email','','邮箱已存在',self::EXISTS_VALIDATE,'unique'],
        ['tel','require','手机号码不能为空'],
        ['tel','/^\d{11}$/','手机号码不合法',self::EXISTS_VALIDATE,'regex'],
        ['email','','邮箱已存在',self::EXISTS_VALIDATE,'unique'],
        //['checkcode','require','图片验证码不能为空'],
        //['checkcode','checkImgCode','图验证码不正确',self::EXISTS_VALIDATE,'callback'],
        ['captcha','require','手机验证码不能为空'],
        ['captcha','checkTelCode','手机验证码不正确',self::EXISTS_VALIDATE,'callback'],
    ];

    protected $_auto = [
        ['add_time',NOW_TIME,'reg'],
        ['salt','\Org\Util\String::randString','reg','function'],
        ['register_token','\Org\Util\String::randString','reg','function',32],
        ['status',0],//没有验证邮箱的
    ];

    //验证图片
    protected function checkImgCode($code){
        $verify = new \Think\Verify();
        return $verify->check($code);
    }
    //验证手机
    protected function checkTelCode($code){
        if($code == session('reg_tel_code')){
            session('reg_tel_code',null);
            return true;
        }else{
            return false;
        }
    }

    public function addMember(){
        //加盐
        $this->data['password'] = salt_mcrypt($this->data['password'],$this->data['salt']);
        $register_token = $this->data['register_token'];
        $email = $this->data['email'];
        if($this->add() === false){
            return false;
        }
        $url = U('Member/active',['email'=>$email,'register_token'=>$register_token],true,true);
        $subject = '欢迎注册';
        $content = '欢迎你注册我们的网站,请点击<a href="'.$url.'">链接</a>激活账号.如果无法点击,请复制以下链接粘贴到浏览器窗口打开!<br />'.$url;
        $rst = sendMail($email,$subject,$content);
        if($rst['status']){
            return true;
        }else{
            $this->error = $rst['msg'];
            return false;
        }

    }

    public function login(){
        $username = $this->data['username'];
        $password = $this->data['password'];
        if(!$userinfo = $this->getByUsername($username)){
            $this->error = '用户名或密码错误';
            return false;
        }
        if(salt_mcrypt($password,$userinfo['salt']) != $userinfo['password']){
            $this->error = '用户名或密码错误';
            return false;
        }
        //记录用户登录时间和ip
        $data = [
            'id'=>$userinfo['id'],
            'last_login_time'=>NOW_TIME,
            'last_login_ip'=>get_client_ip(1),
        ];
        $this->setField($data);
        session('USERINFO',$userinfo);
        //自动验证
        //删除用户相关的token记录
        $memberTokenModel = M('memberToken');
        $memberTokenModel->delete($userinfo['id']);
        //自动登陆相关
        if(I('post.remember')){
            //生成cookie和数据表数据
            $data = [
                'member_id' => $userinfo['id'],
                'token'    => \Org\Util\String::randString(40),
            ];

            cookie('USER_AUTO_LOGIN_TOKEN', $data, 604800); //保存一个星期

            $memberTokenModel->add($data);
        }
        //获取cookie中的数据存入数据库中
        $shoppingCarModel = D('ShoppingCar');
        $shoppingCarModel->cookie2db();
        cookie(C('SHOPPING_CAR_COOKIE_KEY'),null);
        return $userinfo;

    }
    //自动验证
    public function autoLogin() {
        //从cookie中取出数据
        $data = cookie('USER_AUTO_LOGIN_TOKEN');
        if (!$data) {
            return false;
        }
        //和数据表中的对比
        $memberTokenModel = M('memberToken');
        if (!$memberTokenModel->where($data)->count()) {
            return false;
        }
        $memberTokenModel->delete($data['member_id']);
        //生成cookie和数据表数据
        $data = [
            'member_id' => $data['member_id'],
            'token'    => \Org\Util\String::randString(40),
        ];
        cookie('USER_AUTO_LOGIN_TOKEN', $data, 604800);
        $memberTokenModel->add($data);
        $userinfo = $this->find($data['member_id']);
        session('USERINFO',$userinfo);
        return $userinfo;
    }
}