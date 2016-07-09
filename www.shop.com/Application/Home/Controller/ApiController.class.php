<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/7/5
 * Time: 16:57
 */

namespace Home\Controller;


use Think\Controller;

class ApiController extends Controller{
    public function regSms($tel) {
        //发送短信
        //引入topSdk.php
        Vendor('Alidayu.TopSdk');
        //$tel = '17098331257';
        $c            = new \TopClient;
        $c->appkey    = '23398940';//ak
        $c->secretKey = '2f12162fc74848d33ba686ed64d99398';//sk
        $req          = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("亮w123");//前面

        $code = \Org\Util\String::randNumber(100000, 999999);
        //保存到session中
        session('reg_tel_code',$code);
        $data = [
            'name'=>'快看',
            'code'=> $code,
        ];
        $req->setSmsParam(json_encode($data));
        $req->setRecNum($tel);
        $req->setSmsTemplateCode("SMS_11540340");//模板
        $resp         = $c->execute($req);
        dump($resp);
    }
}