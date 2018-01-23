<?php

namespace kriss\bd\sms\examples;

use kriss\bd\sms\AbstractSmsSender;

class SmsSender extends AbstractSmsSender
{
    const TYPE_REGISTER = 'register';
    const TYPE_RESET_PASSWORD = 'reset_password';

    /**
     * 创世华信后台报备过的内容：
     * 格式为将后台报备时的 @ 替换成 {code} 的形式
     * 例如：
     * 短信后台报备：【创世华信】您的验证码是@，10分钟内有效。
     * 此处写的格式：【创世华信】您的验证码是{code}，10分钟内有效。
     * @var array
     */
    public static $contentData = [
        self::TYPE_REGISTER => '【创世华信】您的短信验证码是：{code}，10分钟内有效。',
        self::TYPE_RESET_PASSWORD => '【创世华信】您正在重置密码，您短信验证码是：{code}，10分钟内有效。',
    ];
}