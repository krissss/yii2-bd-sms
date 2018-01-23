<?php

namespace kriss\bd\sms\examples;

use app\models\User;
use yii\base\NotSupportedException;

class ResetPasswordForm extends RegisterForm
{
    const SMS_SENDER_TYPE = SmsSender::TYPE_RESET_PASSWORD;

    public function register()
    {
        throw new NotSupportedException('请使用 resetPassword 方法');
    }

    /**
     * @return false|User
     */
    public function resetPassword()
    {
        $user = User::find()->where(['cellphone' => $this->cellphone])->one();
        if (!$user) {
            $this->addError('cellphone', '该手机号未注册');
            return false;
        }
        $user->setPassword($this->password);
        $user->save(false);
        return $user;
    }
}