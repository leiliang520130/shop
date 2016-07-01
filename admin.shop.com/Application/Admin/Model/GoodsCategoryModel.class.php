<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/6/27
 * Time: 19:16
 */

namespace Admin\Model;


use Think\Model;

class GoodsCategoryModel extends Model{
    protected $patchValidate = true;//开启批量验证

    protected $_validate = [
        ['name','require','商品分类名称不能为空'],
        ['status','0,1','显示状态不合法',self::EXISTS_VALIDATE,'in'],
    ];

    public function getList() {
        return $this->where(['status'=>['egt',0]])->order('lft')->select();
    }

    public function addCategory(){
        unset($this->data[$this->getPk()]);
        $orm = D('MySQL','Logic');
        $nestedsets = new \Admin\Logic\NestedSets($orm,$this->trueTableName,'lft','rght','parent_id','id','level');
        return $nestedsets->insert($this->data['parent_id'],$this->data,'bottom');
    }
    //修改的方法
    public function saveCategory(){
        $parent_id = $this->getFieldById($this->data['id'],'parent_id');
        if($this->data['parent_id'] != $parent_id){
            $orm = D('MySQL','Logic');
            $nestedsets = new \Admin\Logic\NestedSets($orm,$this->trueTableName,'lft','rght','parent_id','id','level');
            if($nestedsets->moveUnder($this->data['id'],$this->data['parent_id'],'bottom') === false){
                $this->error = '不能将分类移动到子分类下';
                return false;

            }
        }
        return $this->save();
    }

    //删除的方法
    public function deleteCategory($id){
        $orm = D('MySQL','Logic');
        $nestedsets = new \Admin\Logic\NestedSets($orm,$this->trueTableName,'lft','rght','parent_id','id','level');
        return $nestedsets->delete($id);
    }
}