<?php

namespace kriss\bd\sms\examples;

use app\models\User;
use kriss\bd\sms\CellphoneValidator;
use kriss\bd\sms\SmsCodeHelper;
use yii\base\Model;

class RegisterForm extends Model
{
    const SMS_SENDER_TYPE = SmsSender::TYPE_REGISTER;

    public $cellphone;
    public $sms_code;
    public $password;

    public function rules()
    {
        return [
            [['cellphone', 'sms_code', 'password'], 'required'],
            ['cellphone', CellphoneValidator::className()],
            ['password', 'string', 'min' => 6],
            ['sms_code', 'validateSmsCode'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'cellphone' => '手机号',
            'sms_code' => '短信验证码',
            'password' => '密码',
        ];
    }

    /**
     * @param $attribute
     */
    public function validateSmsCode($attribute)
    {
        if (!SmsCodeHelper::validateCode(static::SMS_SENDER_TYPE, $this->cellphone, $this->$attribute)) {
            $this->addError($attribute, '短信验证码验证失败');
        }
    }

    /**
     * @return false|User
     */
    public function register()
    {
        $user = User::find()->where(['cellphone' => $this->cellphone])->one();
        if ($user) {
            $this->addError('cellphone', '手机号已被注册');
            return false;
        }
        $user = User::createByCellphone($this->cellphone, $this->password);
        return $user;
    }

}