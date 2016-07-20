<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/12
 * Time: 21:12
 */

namespace Home\Controller;


use Think\Controller;

class OrderInfoController extends Controller{
    /**
     * @var \Home\Model\OrderInfoModel
     */
    private $model = null;
    protected function _initialize(){
        $this->model = D('OrderInfo');
    }

    /**
     * 1.接收数据
     * 2·创建订单
     * 3.提示跳转
     */
    public function add() {
        if(IS_POST){
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->addOrder() === false){
                $this->error(get_error($this->model));
            }
            $this->success('创建订单成功',U('Cart/flow3'));
        }else{
            $this->error('拒绝访问');
        }
    }
    /**
     * 展示用户列表
     */
    public function index(){
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

        //展示已买商品信息
        $rows = $this->model->getList();
        $this->assign('rows',$rows);
        $this->assign('statuses',$this->model->statuses);
        $this->display();
    }

    //完成订单
    public function finish($id) {
        $order_info_model = M('OrderInfo');
        if($order_info_model->where(['id'=>$id])->setField('status',4)===false){
            $this->error(get_error($order_info_model));
        }else{
            $this->success('交易完成',U('index'));
        }
    }


}