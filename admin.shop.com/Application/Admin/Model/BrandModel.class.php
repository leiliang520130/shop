<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/6/25
 * Time: 9:44
 */

namespace Admin\Model;


use Think\Model;
use Think\Upload;

class BrandModel extends Model{
    protected $patchValidate = true;//开启批量验证

    protected $_validate = [
        ['name','require','品牌不能为空'],
        ['status','0,1','显示状态不合法',self::EXISTS_VALIDATE,'in'],
        ['sort','number','排序必须为数组'],
    ];
    //分页搜索
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

    public function addBrand($file){
        $upload_setting = C('UPLOAD_SETTING');
        $upload = new Upload($upload_setting);
        $info = $upload->uploadOne($file);
        $logo_path = $info['savepath'].$info['savename'];
        $this->data['logo'] = $logo_path;
        return $this->add();

    }

    public function saveBrand($file){
        if(!empty($file['name'])){
            $upload_setting = C('UPLOAD_SETTING');
            $upload = new Upload($upload_setting);
            $info = $upload->uploadOne($file);
            $logo_path = $info['savepath'].$info['savename'];
            $this->data['logo'] = $logo_path;
        }

        return $this->save();

    }

    public function getList() {
        return $this->where(['status'=>['gt',0]])->select();
    }

}