<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/14
 * Time: 22:30
 */

namespace Home\Controller;


use Think\Controller;

class SearchController extends Controller{
    //全文搜索技术
    public function search(){
        //判断是否需要展示商品分类,首页展示,其它页面折叠
        $this->assign('show_category', false);
        //由于分类数据和帮助文章列表数据,不会频繁发生变化,但是请求又较为频繁,所以我们进行缓存
        if (!$goods_categories = S('goods_categories')) {
            //准备商品分类数据
            $goods_category_model = D('GoodsCategory');
            $goods_categories = $goods_category_model->getList('id,name,parent_id');
            S('goods_categories', $goods_categories,3600);
        }
        $this->assign('goods_categories', $goods_categories);


        if (!$help_article_list = S('help_article_list')) {
            //准备商品分类数据
            $article_category_model = D('Article');
            $help_article_list = $article_category_model->getHelpList();
            S('help_article_list', $help_article_list,3600);
        }
        //帮助文章分类
        $this->assign('help_article_list',$help_article_list);

        //获取用户登陆信息
        $this->assign('userinfo',session('USERINFO'));

        $keyword = I('get.keyword');
        header('Content-Type:text/html; charset=utf-8');
        vendor('Sphinx.sphinxapi');
        $sphinx = new \SphinxClient();
        $sphinx->SetServer('127.0.0.1',9312);
        $sphinx->SetLimits(0, 50);
        $sphinx->SetMatchMode(SPH_MATCH_ANY);
        $rst = $sphinx->Query($keyword);
        $goods_ids = array_keys($rst['matches']);
        if(empty($goods_ids)){
            echo "<script>alert('没有此商品');location.href='".U('Index/index')."'</script>";
        }else{
            $model = M('Goods');
            $list = $model->where(['id'=>['in',$goods_ids]])->select();
            $this->assign('list',$list);
            $this->display();
        }

    }

    public function spinx($keyword){
        vendor('Sphinx.sphinxapi');
        $spinx = new \SphinxClient();
        $spinx->SetServer('127.0.0.1', 9312);
        $spinx->SetMatchMode(SPH_MATCH_ANY);
        $rst = $spinx->Query($keyword);
        $goods_ids = array_keys($rst['matches']);
        if(!empty($goods_ids)) {
            $list = M('Goods')->field('id,name,stock')->where(['id'=>['in',$goods_ids]])->select();
            $this->ajaxReturn($list);
        }else{
            $this->ajaxReturn(false);
        }
    }
}