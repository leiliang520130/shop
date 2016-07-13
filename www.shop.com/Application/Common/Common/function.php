<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/6/24
 * Time: 23:19
 */


function get_error(\Think\Model $model){
    $errors = $model->getError();
    if(!is_array($errors)){
        $errors = [$errors];
    }
    $html = '<ol>';
    foreach($errors as $error){
        $html .='<li>'.$error.'</li>';
    }
    $html .='</ol>';
    return $html;
}

function arr2select(array $data, $name_field = 'name', $value_field = 'id', $name = '',$default_value='') {
    $html = '<select name="' . $name . '" class="' . $name . '">';
    $html .= '<option value=""> 请选择 </option>';
    foreach ($data as $key => $value) {
        //由于get和post提交的数据都是字符串,所以可能存在数字0和空字符串相等的问题
        //我们将遍历的数据变成string,然后强制类型比较.
        if((string)$value[$value_field] === $default_value){
            $html .= '<option value="' . $value[$value_field] . '" selected="selected">' . $value[$name_field] . '</option>';
        }else{
            $html .= '<option value="' . $value[$value_field] . '">' . $value[$name_field] . '</option>';
        }
    }
    $html .= '</select>';
    return $html;
}

function salt_mcrypt($str,$salt){
    return md5(md5($str).$salt);
}

function sendMail($email,$subject,$content){
    Vendor('PHPMailer.PHPMailerAutoload');
    $mail = new \PHPMailer;

    $mail->isSMTP();
    $mail->Host       = 'smtp.qq.com';  //填写发送邮件的服务器地址
    $mail->SMTPAuth   = true;            // 使用smtp验证
    $mail->Username   = '1031041088@qq.com'; // 发件人账号名
    $mail->Password   = 'cdmylmsjbwrqbfjf';            // 授权码
    $mail->SMTPSecure = 'ssl';               // 使用协议,具体是什么根据你的邮件服务商来确定
    $mail->Port       = 465;                   // 使用的端口

    $mail->setFrom('1031041088@qq.com', '呵呵哒');//发件人,注意:邮箱地址必须和上面的一致
    $mail->addAddress($email);     // 收件人

    $mail->isHTML(true);                                  // 是否是html格式的邮件

    $mail->Subject = $subject;//标题
    $mail->Body    = $content;//正文
    $mail->CharSet = 'UTF-8';

    if($mail->send()){
        return [
            'status'=>1,
            'msg'=>'发送成功',
        ];
    } else{
        return [
            'status'=>0,
            'msg'=>$mail->ErrorInfo,
        ];

    }
}

//实例化redis
function get_redis(){
    $redis = new Redis();
    $redis->connect(C('REDIS_HOST'),C('REDIS_PORT'));
    return $redis;
}

/**
 * 本地金钱表示形式：900 表示为 900.00
 * @param $number
 * @return string
 */
function locate_number_format($number){
    return number_format($number,2,'.','');
}