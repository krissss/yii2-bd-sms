Yii2 必答短信的组件
============
> Yii2 必答短信的组件，仅适用于`必答短信`

Installation
------------

```
php composer.phar require --prefer-dist kriss/yii2-bd-sms -vvv
```

Usage
------------

1. 定义一个 SmsSender 继承 AbstractSmsSender

see [SmsSender.php](https://github.com/krissss/yii2-bd-sms/blob/master/examples/SmsSender.php)

2. 配置 config

```php
use kriss\bd\sms\Sms;

$config = [
    'components' => [
        Sms::COMPONENT_NAME => [
            'class' => Sms::className(),
            'enable' => true,
            'account' => 'xxxx',
            'password' => 'xxxxx',
            'logCategory' => 'bd-sms',
        ]
    ]
]
```

3. Controller

see [AppController.php](https://github.com/krissss/yii2-bd-sms/blob/master/examples/AppController.php)
