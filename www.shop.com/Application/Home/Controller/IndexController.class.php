<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    //初始化方法
    public function _initialize(){
        if(ACTION_NAME == 'index'){
            $show_category = true;
        }else{
            $show_category = false;
        }
        $this->assign('show_category',$show_category);
        //商品分类通用显示
        if(!$goods_categories = S('goods_categories')){
            $goodsCategoryModel = D('GoodsCategory');
            $goodsCategory = $goodsCategoryModel->getList('id,name,parent_id');
            S('goods_categories',$goods_categories,3600);
        }
        $this->assign('goods_categories',$goodsCategory);
        //显示帮助信息
        if(!$help_article_list = S('help_article_list')){
            $articleCategoryModel = D('Article');
            $help_article_list = $articleCategoryModel->getHelpList();
            S('help_article_list',$help_article_list,3600);
        }
        $this->assign('help_article_list',$help_article_list);
        //用户登录信息
        $this->assign('userinfo',session('USERINFO'));
    }

    public function index(){
        $goodsModel = D('Goods');
        $data = [
            'goods_best_list'=>$goodsModel->getListByGoodsStatus(1),
            'goods_new_list'=>$goodsModel->getListByGoodsStatus(2),
            'goods_hot_list'=>$goodsModel->getListByGoodsStatus(4),
        ];
        $this->assign($data);
        $this->display();
    }

    public function goods($id){
        $goodsModel = D('Goods');
        $row = $goodsModel->getGoodsInfo($id);
        if(!$row){
            $this->error('你查看的商品不存在或者已经下架',U('index'));
        }
        $this->assign('row',$row);
        $this->display();
    }

    //显示帮助信息文字内容
    public function content($id){
        $article = M('Article');
        $row = $article->find($id);
        $this->assign('row',$row);
        $contentModel = M('ArticleContent');
        $content = $contentModel->find($id);
        $this->assign('content',$content);
        $this->display();
    }

}