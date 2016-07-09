<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/7
 * Time: 19:12
 */

namespace Home\Model;


use Think\Model;

class ArticleModel extends Model{
    public function getHelpList(){
        //获取文章分类
        $articleCategoryModel = D('ArticleCategory');
        $article_categories = $articleCategoryModel->where(['status'=>1,'is_help'=>1])->getField('id,name');
        $data = [];
        foreach($article_categories as $key=>$value){
            $articles = $this->field('id,name')->order('sort')->limit(6)->where(['status'=>1,'article_category_id'=>$key])->select();
            $data[$value] = $articles;
        }

        return $data;
    }
}