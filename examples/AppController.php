<?php

namespace kriss\bd\sms\examples;

use app\models\User;
use kriss\bd\sms\SmsCodeHelper;
use Yii;
use yii\base\Model;
use yii\web\Controller;

class AppController extends Controller
{
    // 注册
    public function actionRegister()
    {
        return $this->loginRegisterRestPassword(RegisterForm::className(), 'register');
    }

    // 重置密码
    public function actionResetPassword()
    {
        return $this->loginRegisterRestPassword(ResetPasswordForm::className(), 'resetPassword');
    }

    // 注册发短信
    public function actionRegisterSms()
    {
        return $this->sendSmsCode(SmsSender::TYPE_REGISTER);
    }

    // 重置密码发短信
    public function actionResetPasswordSms()
    {
        return $this->sendSmsCode(SmsSender::TYPE_RESET_PASSWORD);
    }

    /**
     * 登陆、注册、重置密码
     * @param $formClass
     * @param $doFunction
     * @return User|false|Model
     */
    protected function loginRegisterRestPassword($formClass, $doFunction)
    {
        /** @var Model $model */
        $model = new $formClass();

        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            /** @var false|User $user */
            $user = $model->$doFunction();
            if ($user !== false) {
                return $user;
            }
        }

        return $model;
    }

    /**
     * 发送验证码短信
     * @param $smsSenderType
     * @return SmsSender|string|\yii\base\Model
     * @throws \Exception
     */
    protected function sendSmsCode($smsSenderType)
    {
        $sms = new SmsSender(['type' => $smsSenderType]);

        if ($sms->load(Yii::$app->request->post(), '') && $sms->validate()) {
            $code = SmsCodeHelper::generateCode($smsSenderType, $sms->cellphone, 600);
            if ($sms->isEnable()) {
                $sms->contentParams = ['code' => $code];
                $isOk = $sms->send();
                return $isOk ? '发送成功' : '发送失败，请联系管理员';
            } else {
                return $this->validateError('短信未开放，短信验证码为：' . $code);
            }
        }

        return $sms;
    }
}