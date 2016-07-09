<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/2
 * Time: 10:56
 */

namespace Admin\Model;


use Think\Model;

class AdminModel extends Model{
    protected $patchValidate = true;//开启批量验证

    protected $_validate = [
        ['username','require','用户名不能为空'],
        ['username','','用户名已存在',self::EXISTS_VALIDATE,'unique','register'],
        ['password','require','密码不能为空'],
        ['password','6,16','密码强度不合法',self::EXISTS_VALIDATE,'length'],
        ['repassword','password','两次密码不一致',self::EXISTS_VALIDATE,'confirm'],
        ['email','require','邮箱必填'],
        ['email','','邮箱已存在',self::EXISTS_VALIDATE,'unique'],
        ['email','email','邮箱不合法'],
        ['resetrepassword','resetpassword','两次密码不一致',self::EXISTS_VALIDATE,'confirm'],
        //['captcha','checkCaptcha','验证码不合法',self::EXISTS_VALIDATE,'callback'],
    ];
    //自动完成
    protected $_auto = array(
        array('add_time',NOW_TIME,self::MODEL_INSERT),
        array('salt','\Org\Util\String::randString',self::MODEL_INSERT,'function',[6]),
    );

    //验证码
    protected function checkCaptcha($code) {
        //创建验证码对象
        $verify = new \Think\Verify();
        return $verify->check($code);
    }
    //添加管理员方法
    public function addAdmin(){
        //添加用户
        $this->data['password'] = salt_mcrypt($this->data['password'],$this->data['salt']);
        $admin_id = $this->add();
        if($admin_id === false){
            return false;
        }
        //添加用户角色关系
        $role_ids = I('post.role_id');
        $data = [];
        foreach($role_ids as $role_id){
            $data[] = [
                'admin_id'=>$admin_id,
                'role_id'=>$role_id,
            ];
        }
        if(!empty($data)){
            $adminRoleModel = M('AdminRole');
            $rst = $adminRoleModel->addAll($data);
            if($rst === false){
                return false;
            }
        }
        return true;
    }

    public function getAdminInfo($id){
        $row = $this->find($id);
        $adminRoleModel = D('AdminRole');
        $row['role_ids'] = json_encode($adminRoleModel->where(['admin_id'=>$id])->getField('role_id',true));
        return $row;
    }

    public function saveAdmin($id){
        //保存管理员角色关联
        //删除之前的关联
        $adminRoleModel = M('AdminRole');
        if($adminRoleModel->where(['admin_id'=>$id])->delete() === false){
            $this->error = '删除原有角色失败';
            return false;
        }
        //添加用户角色关系
        $role_ids = I('post.role_id');
        $data = [];
        foreach($role_ids as $role_id){
            $data[] = [
                'admin_id'=>$id,
                'role_id'=>$role_id,
            ];
        }
        if(!empty($data)){
            $adminRoleModel = M('AdminRole');
            $rst = $adminRoleModel->addAll($data);
            if($rst === false){
                return false;
            }
        }
        return true;
    }

    //添加一个删除方法
    public function deleteAdmin($id){
        if($this->delete($id) === false){
            return false;
        }
        //删除的关联
        $adminRoleModel = M('AdminRole');
        if($adminRoleModel->where(['admin_id'=>$id])->delete() === false){
            $this->error = '删除原有角色失败';
            return false;
        }
        return true;
    }

    //显示及其分页
    public function getPageResult(array $cond){
        //总行数
        $count = $this->where($cond)->count();
        //获取配置
        $page_setting = C('PAGE_SETTING');
        //工具类对象
        $page = new \Think\Page($count, $page_setting['PAGE_SIZE']);
        //设置主题
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        //获取分页代码
        $page_html = $page->show();
        //获取分页数据
        $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();
        return compact('rows', 'page_html');
    }

    public function saveresetpassword($id){
        $str = new \Org\Util\String();
        $salt = $str->randString();
        $data['id'] = $id;
        $data['salt'] = $salt;
        $password = I('post.resetpassword');
        if(empty($password)){
            $str = $str->randString();
            $data['password'] = salt_mcrypt($str,$salt);
            if($this->save($data) === false){
                $this->error = "密码修改失败";
                return false;
            }
            return $str;
        }else{
            $data['password'] = salt_mcrypt($password,$salt);
            if($this->save($data) === false){
                $this->error = "密码修改失败";
                return false;
            }
            return true;
        }
    }
    //登录验证
    public function login(){
        //获取用用户信息取点盐
        $username = $this->data['username'];
        $password = $this->data['password'];
        $userinfo = $this->getByUsername($username);
        if(!$userinfo){
            $this->error = "用户名或密码错误";
            return false;
        }
        //验证密码
        $salt_password = salt_mcrypt($password,$userinfo['salt']);
        if($salt_password != $userinfo['password']){
            $this->error = "用户名或密码错误";
            return false;
        }

        $data = [
            'last_login_time'=>NOW_TIME,
            'last_login_ip'=>  get_client_ip(1),
            'id'=>$userinfo['id'],
        ];
        $this->save($data);
        session('USERINFO',$userinfo);
        //获取用户权限
        $this->getPermissions($userinfo['id']);
        //删除用户相关的token记录
        $adminTokenModel = M('AdminToken');
        $adminTokenModel->delete($userinfo['id']);
        //自动登陆相关
        if(I('post.remember')){
            //生成cookie和数据表数据
            $data = [
                'admin_id' => $userinfo['id'],
                'token'    => \Org\Util\String::randString(40),
            ];

            cookie('USER_AUTO_LOGIN_TOKEN', $data, 604800); //保存一个星期

            $adminTokenModel->add($data);
        }
        return $userinfo;
    }
    //获取权限实现方法
    private function getPermissions($admin_id){
        $cond=[
            'path'=>['neq',''],
            'admin_id'=>$admin_id,
        ];
        $permissions = M()->distinct(true)->field('permission_id,path')->table('admin_role')->alias('ar')->join('__ROLE_PERMISSION__ as rp ON ar.`role_id`=rp.`role_id`')->join('__PERMISSION__ as p ON p.`id`=rp.`permission_id`')->where($cond)->select();
        $pids = [];
        $paths = [];
        foreach($permissions as $permission){
            $paths[] = $permission['path'];
            $pids[] = $permission['permission_id'];
        }
        session('PATHS',$paths);
        session('PIDS',$pids);
        return true;
    }

    public function autoLogin() {
        //从cookie中取出数据
        $data = cookie('USER_AUTO_LOGIN_TOKEN');
        if (!$data) {
            return false;
        }
        //和数据表中的对比
        $adminTokenModel = M('AdminToken');
        if (!$adminTokenModel->where($data)->count()) {
            return false;
        }
        $adminTokenModel->delete($data['admin_id']);
        //生成cookie和数据表数据
        $data = [
            'admin_id' => $data['admin_id'],
            'token'    => \Org\Util\String::randString(40),
        ];
        cookie('USER_AUTO_LOGIN_TOKEN', $data, 604800);
        $adminTokenModel->add($data);
        $userinfo = $this->find($data['admin_id']);
        session('USERINFO',$userinfo);
        $this->getPermissions($userinfo['id']);
        return $userinfo;
    }
}