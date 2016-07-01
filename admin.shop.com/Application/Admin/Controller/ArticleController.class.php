<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/6/25
 * Time: 11:18
 */

namespace Admin\Controller;


use Think\Controller;

class ArticleController extends Controller{
    /**
     * @var \Admin\Model\ArticleModel
     */
    protected $model = null;
    public function _initialize(){
        $this->model = D('Article');
    }

    public function index(){
        $name = I('get.name');
        $cond['article.status'] = ['egt',0];
        if($name){
            $cond['article.name'] = ['like','%'.$name.'%'];
        }
        $data = $this->model->getPage($cond);
        $this->assign($data);
        //获取所有的文章分类
        $article_category_model = D('ArticleCategory');
        $categories             = $article_category_model->where(['status'=>['egt',0]])->getField('id,name');
        $this->assign('categories', $categories);
        $this->display();
    }
    //添加文章
    public function add(){
        if(IS_POST){
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->addAritcle(I('post.')) === false){
                $this->error(get_error($this->model));
            }else{
                $this->success('添加成功',U('index'));
            }
        }else{
            $cond['status'] = ['egt',0];
            $categoryModel = D('ArticleCategory');
            $categorys = $categoryModel->where($cond)->select();
            $this->assign('categorys',$categorys);
            $this->display();
        }
    }
    //文章获取分类
    public function category(){
        $cond['status'] = ['egt',0];
        $categoryModel = D('ArticleCategory');
        $rows = $categoryModel->where($cond)->select();
        $this->assign('rows',$rows);
        $this->display();
    }
    //添加文章分类
    public function categoryAdd(){
        if(IS_POST){
            $categoryModel = D('ArticleCategory');
            if($categoryModel->create() === false){
                $this->error(get_error($this->model));
            }
            if($categoryModel->add() === false){
                $this->error(get_error($this->model));
            }else{
                $this->success('添加成功',U('category'));
            }
        }else{
            $this->display('categoryAdd');
        }
    }
    //分类修改
    public function categoryEdit($id){
        $categoryModel = D('ArticleCategory');
        if(IS_POST){
            if($categoryModel->create() === false){
                $this->error('修改失败');
            }
            if($categoryModel->save() === false){
                $this->error('修改失败');
            }
            $this->success('修改成功',U('category'));
        }else{
            $row = $categoryModel->find($id);
            $this->assign('row',$row);
            $this->display('categoryAdd');
        }
    }
    //分类删除
    public function categoryRemove($id){
        $categoryModel = D('ArticleCategory');
        $data = [
            'id'=>$id,
            'status'=>-1,
            'name'=>['exp','concat(name,"_del")'],
        ];
        if($categoryModel->setField($data) === false){
            $this->error('删除失败');
        }else{
            $this->success('删除成功',U('category'));
        }
    }
    //显示文字内容
    public function content($id){
        $row = $this->model->find($id);
        $this->assign('row',$row);
        $contentModel = M('ArticleContent');
        $content = $contentModel->find($id);
        $this->assign('content',$content);
        $this->display();
    }

    //修改文章
    public function edit($id){
        if(IS_POST){
            if($this->model->create() === false){
                $this->error(get_error($this->model));
            }
            if($this->model->saveAritcle(I('post.')) === false){
                $this->error(get_error($this->model));
            }
            $this->success('修改成功',U('index'));
        }else{
            $row = $this->model->find($id);
            //查询分类信息
            $categoryModel = M('ArticleCategory');
            $cond['status'] = ['egt',0];
            $categorys = $categoryModel->where($cond)->select();
            //查询内容
            $contentModel = M('ArticleContent');
            $content = $contentModel->find($id);
            $this->assign('row',$row);
            $this->assign('categorys',$categorys);
            $this->assign('content',$content);
            $this->display('add');
        }
    }
    //删除文章
    public function remove($id){
        $data = [
            'id'=>$id,
            'status'=>-1,
            'name'=>['exp','concat(name,"_del")'],
        ];
        if($this->model->setField($data) === false){
            $this->error('删除失败');
        }else{
            $this->success('删除成功',U('index'));
        }
    }
}