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

        //判断是否需要展示商品分类,首页展示,其它页面折叠
        $this->assign('show_category', true);
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