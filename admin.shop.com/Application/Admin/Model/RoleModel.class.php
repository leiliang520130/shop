<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/1
 * Time: 23:03
 */

namespace Admin\Model;


use Think\Model;

class RoleModel extends Model{
    //显示方法
    public function getPageResult(array $cond){
        //查询条件的拼接
        $cond = array_merge(['status'=>1],$cond);
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

    //添加方法
    public function addRole(){
        $this->startTrans();
        if(($role_id = $this->add()) === false){
            $this->rollback();
            return false;
        }
        $permission_ids = I('post.permission_id');
        $data = [];
        foreach($permission_ids as $permission_id){
            $data[] = [
                'role_id'=>$role_id,
                'permission_id'=>$permission_id,
            ];
        }
        if($data){
            $role_permission_model = M('RolePermission');
            if($role_permission_model->addAll($data) ===false){
                $this->error = '保存权限失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }
    //查询编辑时候的权限信息
    public function getPermissionInfo($id){
        //1.获取基本信息
        $row = $this->find($id);
        //2.获取关联权限信息
        $rolePermissionModel = M('RolePermission');
        $row['permission_ids'] = json_encode($rolePermissionModel->where(['role_id'=>$id])->getField('permission_id',true));
        return $row;
    }
    //编辑的保存信息
    public function saveRole(){
        $this->startTrans();
        $role_id = $this->data['id'];
        //保存基本信息
        if ($this->save() === false){
            $this->rollback();
            return false;
        }
        //删除原有的
        $rolePermissionModel = M('RolePermission');
        if($rolePermissionModel->where(['role_id'=>$role_id])->delete() === false){
            $this->error = '删除历史权限失败';
            $this->rollback();
            return false;
        }
        //保存关联的权限
        $permission_ids = I('post.permission_id');
        $data = [];
        foreach($permission_ids as $permission_id){
            $data[] = [
                'role_id'=>$role_id,
                'permission_id'=>$permission_id,
            ];
        }
        if($data){
            if($rolePermissionModel->addAll($data) ===false){
                $this->error = '保存权限失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    public function deleteRole($id){
        //开启事务支持
        $this->startTrans();
        //删除角色记录
        if($this->delete($id) === false){
            $this->rollback();
            return false;
        }

        //删除权限关联
        $rolePermissionModel = M('RolePermission');
        if($rolePermissionModel->where(['role_id'=>$id])->delete()===false){
            $this->error = '删除权限关联失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

}