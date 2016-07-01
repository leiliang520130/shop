<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/6/24
 * Time: 18:10
 */

namespace Admin\Model;


use Think\Model;

class SupplierModel extends Model{
    protected $patchValidate = true;//开启批量验证

    protected $_validate = [
        ['name','require','供货商不能为空'],
        ['name','','供货商已存在',self::EXISTS_VALIDATE,'unique'],
        ['status','0,1','供货商状态不合法',self::EXISTS_VALIDATE,'in'],
        ['sort','number','排序必须为数组'],
    ];


    public function getPage(array $cond=[]){
        //获取分页配置信息
        $page_setting = C('PAGE_SETTING');
        $count = $this->where($cond)->count();
        $page = new \Think\Page($count,$page_setting['PAGE_SIZE']);
        $page->setConfig('theme',$page_setting['PAGE_THEME']);
        $page_html = $page->show();
        $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();

        return compact('rows','page_html');

    }

    public function getList() {
        return $this->where(['status'=>['gt',0]])->select();
    }
}