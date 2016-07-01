<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/1
 * Time: 18:27
 */

namespace Admin\Model;


use Think\Model;

class PermissionModel extends Model{
    protected $patchValidate = true;
    //自动验证
    protected $_validate     = [
        ['name', 'require', '权限名称不能为空'],
    ];
    public function getList(){
        return $this->where(['status'=>1])->order('lft')->select();
    }

    public function addPermission(){
        unset($this->data[$this->getPk()]);
        $orm = D('MySQL', 'Logic');
        $nestedsets = new \Admin\Logic\NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nestedsets->insert($this->data['parent_id'], $this->data, 'bottom') === false) {
            $this->error = '添加失败';
            return false;
        }
        return true;
    }
    //添加一个修改的方法
    public function savePermission($id){
        $parent_id = $this->getFieldById($id,'parent_id');
        if($parent_id != $this->data['parent_id']){
            $orm        = D('MySQL', 'Logic');
            //创建nestedsets对象
            $nestedsets = new \Admin\Logic\NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
            if ($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom') === false) {
                $this->error = '不能将分类移动到自身或后代分类中!';
                return false;
            }
        }
        return $this->save();
    }

    //添加删除方法
    public function deletePermission($id){
        $this->startTrans();
        //获取后代权限
        $permission_info = $this->field('lft,rght')->find($id);
        $cond = [
            'lft'=>['egt',$permission_info['lft']],
            'rght'=>['elt',$permission_info['rght']],
        ];
        $permission_ids = $this->where($cond)->getField('id',true);
        //删除角色关联权限
        $role_permission_model = M('RolePermission');
        if($role_permission_model->where(['permission_id'=>['in',$permission_ids]])->delete()===false){
            $this->error = '删除角色关联权限失败';
            $this->rollback();
            return false;
        }
        //创建orm
        $orm = D('MySQL', 'Logic');
        //创建nestedsets对象
        $nestedsets = new \Admin\Logic\NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        if($nestedsets->delete($id) === false){
            $this->error = '删除失败';
            $this->rollback();
            return false;
        }else{
            $this->commit();
            return true;
        }

    }
}