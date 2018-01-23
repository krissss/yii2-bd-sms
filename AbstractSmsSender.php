<?php

namespace kriss\bd\sms;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * 短信发送类，需要继承该类，然后定义 const 变量和 $contentData
 */
abstract class AbstractSmsSender extends Model
{
    // 例子
    //const TYPE_REGISTER = 'register';

    /**
     * 创世华信后台报备过的内容：
     * 格式为将后台报备时的 @ 替换成 {code} 的形式
     * 例如：
     * 短信后台报备：【创世华信】您的验证码是@，10分钟内有效。
     * 此处写的格式：【创世华信】您的验证码是{code}，10分钟内有效。
     * @var array
     */
    public static $contentData = [
        // 例子
        //self::TYPE_REGISTER => '【创世华信】您的短信验证码是：{code}，10分钟内有效。',
    ];

    /**
     * 手机号
     * @var string
     */
    public $cellphone;
    /**
     * 发送类型
     * @var integer
     */
    public $type;
    /**
     * 发送短信的替换参数
     * 会替换短信模版中的形如{code}的值
     * 格式配置如下：
     * ['code' => 32510]
     * @var array
     */
    public $contentParams;

    /**
     * @var false|string
     */
    private $_content = false;

    public function rules()
    {
        return [
            ['cellphone', 'required'],
            ['cellphone', CellphoneValidator::className()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cellphone' => '手机号',
        ];
    }

    /**
     * 发送短信
     * @return bool
     */
    public function send()
    {
        return $this->getSms()->send($this->cellphone, $this->getContent());
    }

    /**
     * 是否启用短信
     * @return bool
     */
    public function isEnable()
    {
        return (bool)$this->getSms()->enable;
    }

    /**
     * @return null|object|Sms
     */
    protected function getSms()
    {
        return Yii::$app->get(Sms::COMPONENT_NAME);
    }

    /**
     * 获取修改过后的可发送内容
     * @return string
     * @throws InvalidConfigException
     */
    protected function getContent()
    {
        if ($this->_content !== false) {
            return $this->_content;
        }

        $content = isset(static::$contentData[$this->type]) ? static::$contentData[$this->type] : false;
        if ($content === false) {
            throw new InvalidConfigException('type 必须配置且配置正确');
        }
        $replaces = [];
        foreach ((array)$this->contentParams as $name => $value) {
            $replaces['{' . $name . '}'] = $value;
        }

        $this->_content = ($replaces === []) ? $content : strtr($content, $replaces);
        return $this->_content;
    }
}