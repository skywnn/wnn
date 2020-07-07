<?php
/**
 * Created by PhpStorm.
 * User: Qiu Shui
 * Date: 2020/1/13
 * Time: 16:40
 */

namespace app\api\service;

use app\api\model\MessageModel;
use app\lib\exception\ApiErrorException;
use think\db;
class Message
{
    /**
     * 发送一次雷达消息
     */
    public static function sendMessage($title='', $content='', $msg_from_uid=0, $msg_to_uid,$app_id){
        $messageDate = [
            'app_id' => $app_id,
            'title' => $title,
            'content' => $content,
            'msg_from_uid' => $msg_from_uid,
            'msg_to_uid' => $msg_to_uid,
        ];
        $message = new MessageModel();
        return $message->save($messageDate);
    }
}