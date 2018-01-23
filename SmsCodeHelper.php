<?php

namespace kriss\bd\sms;

use Yii;

/**
 * 短信验证码的帮助类
 */
class SmsCodeHelper
{
    /**
     * 生成 code
     * @param $smsSenderType
     * @param $cellphone
     * @param int $duration
     * @return int
     * @throws \Exception
     */
    public static function generateCode($smsSenderType, $cellphone, $duration = 600)
    {
        $code = random_int(1000, 9999);
        Yii::$app->cache->set([$smsSenderType, $cellphone], $code, $duration);
        return $code;
    }

    /**
     * 验证 code
     * @param $smsSenderType
     * @param $cellphone
     * @param $code
     * @param $removeIfSuccess bool 验证成功后是否移除掉
     * @return bool
     */
    public static function validateCode($smsSenderType, $cellphone, $code, $removeIfSuccess = true)
    {
        $key = [$smsSenderType, $cellphone];
        $realCode = Yii::$app->cache->get($key);
        if ($realCode == $code) {
            if ($removeIfSuccess) {
                Yii::$app->cache->delete($key);
            }
            return true;
        }
        return false;
    }
}