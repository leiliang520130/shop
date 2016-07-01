<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/6/25
 * Time: 11:23
 */

namespace Admin\Model;


use Think\Model;

class ArticleModel extends Model{
    protected $patchValidate = true;//开启批量验证

    protected $_validate = [
        ['name','require','标题不能为空'],
        ['content','require','内容不能为空'],
        ['status','0,1','文章状态不合法',self::EXISTS_VALIDATE,'in'],
        ['sort','number','排序必须为数组'],
    ];

    //文章分页
    public function getPage(array $cond=[]){
        //获取分页配置信息
        $page_setting = C('PAGE_SETTING');
        $count = $this->where($cond)->count();
        $page = new \Think\Page($count,$page_setting['PAGE_SIZE']);
        $page->setConfig('theme',$page_setting['PAGE_THEME']);
        $page_html = $page->show();
        //$rows = $this->join('article_category on article.article_category_id = article_category.id')->field('article.id as id,article.name as name,article.intro as intro,article.`status` as `status`,article.`sort` as `sort`,article.inputtime as inputtime,article_category.name as acname')->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();
        $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();
        return compact('rows','page_html');

    }

    public function addAritcle($post){
        $this->startTrans();
        $id = parent::add();
        if($id === false){
            $this->rollback();
            return false;
        }
        $aritcle['content'] = $post['content'];
        $aritcle['article_id'] = $id;
        $contentModel = M('ArticleContent');
        $rst = $contentModel->add($aritcle);
        if($rst === false){
            $this->rollback();
            return false;
        }

        $this->commit();
    }

    //修改文章
    /**
     *
     * 操作2张表修改数据文章表和内容表
     *
     */
    public function saveAritcle($post){
       // var_dump($post);exit;
        $this->startTrans();
        $rst = parent::save();
        if($rst === false){
            $this->rollback();
            return false;
        }

        $id = $post['id'];
        $contentModel = M('ArticleContent');
        $contentModel->content = $post['content'];
        $rst = $contentModel->where(array('id'=>$id))->save();
        if($rst === false){
            $this->rollback();
            return false;
        }

        return $this->commit();


    }

}