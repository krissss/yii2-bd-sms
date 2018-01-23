<?php

namespace kriss\bd\sms;

use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\httpclient\Client;
use yii\httpclient\Response;

/**
 * 短信接口配置类
 */
class Sms extends BaseObject
{
    // component 名
    const COMPONENT_NAME = 'bd-sms';

    /**
     * @var bool
     */
    public $enable = false;
    /**
     * @var string
     */
    public $logCategory = 'app';
    /**
     * @var string
     */
    public $account;
    /**
     * @var string
     */
    public $password;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var array
     */
    private $commonQuery;

    public function init()
    {
        $this->httpClient = new Client([
            'baseUrl' => 'https://sh2.ipyy.com/smsJson.aspx'
        ]);
        $this->commonQuery = [
            'account' => $this->account,
            'password' => $this->password,
            'action' => 'send',
        ];
    }

    /**
     * 发短信
     * @param $cellphone
     * @param $content
     * @return bool
     */
    public function send($cellphone, $content)
    {
        if (!$this->enable) {
            Yii::warning('短信未启用:手机号:' . $cellphone . ',内容:' . $content, $this->logCategory);
            return false;
        }
        try {
            $result = $this->api('', [
                'mobile' => $cellphone,
                'content' => $content,
            ]);
            if ($result['returnstatus'] == 'Success') {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    /**
     * 执行操作 api
     * @param $uri
     * @param array $params
     * @return array
     * @throws Exception
     */
    protected function api($uri, $params = [])
    {
        if (isset($params['account'])) {
            unset($params['account']);
        }
        if (isset($params['password'])) {
            unset($params['password']);
        }
        Yii::info('发送请求:' . json_encode(['uri' => $uri, 'params' => $params,], JSON_UNESCAPED_UNICODE), $this->logCategory);
        $params = array_merge($this->commonQuery, $params);
        /** @var Response $response */
        $response = $this->httpClient->post($uri, $params)->send();
        if ($response->isOk) {
            $responseContent = $response->getContent();
            Yii::info('发送成功:' . $responseContent, $this->logCategory);
            return json_decode($responseContent, true);
        } else {
            Yii::error('短信接口未联通，错误码：' . $response->statusCode, $this->logCategory);
            throw new Exception('短信接口未联通');
        }
    }
}