<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/18
 * Time: 11:22
 */
return [
    //  +---------------------------------
    //  微信相关配置
    //  +---------------------------------

    // 微信使用code换取用户openid及session_key的url地址
    'login_url' => "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",

    // 微信获取access_token的url地址
    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
        "grant_type=client_credential&appid=%s&secret=%s",


];