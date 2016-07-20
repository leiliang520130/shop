<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/14
 * Time: 15:12
 */

namespace Admin\Controller;


use Think\Controller;

class TestController extends Controller{
    //邮件发送方法多线程
    public function index(){
        $address = 'm15002821257@163.com';
        $pool = [];//地址池
        $start     = microtime(true);
        $obj = new SendMail($address,'测试','测试');
        $pool[] = $obj;
        $obj->start();
        $end = microtime(true);
        echo '共耗时' . ($end - $start) . ' s';

    }

    //全文搜索技术
    public function search($keyword = 'ADC'){
        header('Content-Type:text/html; charset=utf-8');
        vendor('Sphinx.sphinxapi');
        $sphinx = new \SphinxClient();
        $sphinx->SetServer('127.0.0.1',9312);
        $sphinx->SetLimits(0, 50);
        $sphinx->SetMatchMode(SPH_MATCH_ANY);
        $rst = $sphinx->Query($keyword);
        $goods_ids = array_keys($rst['matches']);
        $model = M('Goods');
        $list = $model->where(['id'=>['in',$goods_ids]])->select();
        //关键字高亮
        $options   = array(
            'before_match'    => '<span style="color:red;background:lightblue">',
            'after_match'     => '</span>',
            'chunk_separator' => '...',
            'limit'           => 80, //如果内容超过80个字符，就使用...隐藏多余的的内容
        );
        $keywords  = array_keys($rst['words']);
        foreach ($list as $index => $item) {
            $list[$index] = $sphinx->BuildExcerpts($item, 'mysql', implode(',', $keywords), $options); //使用的索引不能写*，关键字可以使用空格、逗号等符号做分隔，放心，sphinx很智能，会给你拆分的
        }
        var_dump($list);
    }


}




class SendMail extends \Thread{
    private $email,$subject,$content;
    public function __construct($email,$subject,$content) {
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
    }
    public function run() {
        sendMail($this->email, $this->subject, $this->content);
    }
}
