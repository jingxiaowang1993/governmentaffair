<?php

namespace Government\Affair\Classes;

use Government\Affair\Contracts\GatewayInterface;
use Government\Affair\Traits\HasHttpRequest;
use Illuminate\Support\Carbon;

class Base implements GatewayInterface
{
    use HasHttpRequest;

    protected $headers = [];
    protected $params = [];
    private $serviceCode = '';
    private $secretKey = '';
    protected $requestHeadersData = [];
    protected $requestParamsData = [];
    protected $callMethod = '';
    protected $date_time = '';


    public function __construct()
    {
        //应用访问授权码（AK）
        $this->setServiceCode(config('governmentaffair.zjgxfwxt_access_key'));
        //应用访问密钥（SK）
        $this->setSecretKey(config('governmentaffair.zjgxfwxt_secret_key'));
        //调用方式
        $this->setCallMethod(config('governmentaffair.zjgxfwxt_call_method'));
        //网关服务
        $this->gateway();
    }

    public function gateway()
    {
        // TODO: Implement gateway() method.
        if ($this->getCallMethod() == 1) {
            //获取毫秒时间戳
            $time = Carbon::now()->getTimestampMs();
            $headers = [
                'zjgxfwxt-access-key' => $this->getServiceCode(),
                'zjgxfwxt-sign' => md5($this->getServiceCode() . $this->getSecretKey() . $time),//认证签名，签名规则： MD5（AK+SK+时间毫秒值），其签名值小写
                'zjgxfwxt-time' => $time//时间毫秒值（示例：1632643914516）
            ];
        } else {
            $this->setDateTime(Carbon::now()->toRfc7231String());
            $headers = [
                'X-BG-HMAC-ALGORITHM' => 'hmac-sha256',//签名的摘要算法，当前仅支持hmac-sha256
                'X-BG-HMAC-ACCESS-KEY' => $this->getServiceCode(),//分配给应用的accessKey
                'X-BG-DATE-TIME' => $this->getDateTime(),//时间戳，时区为GMT+8，格式为：Tue, 09 Nov 2021 08:49:20 GMT。API服务端允许客户端请求最大时间误差为100秒
            ];
        }

        $this->setHeaders($headers);
        $now_time = date("YmdHis", time());
        $params = [
            'servicecode' => $this->getServiceCode(),//应用访问授权码（AK）
            'time' => $now_time,//时间戳，当前时间年月日时分秒例：2009年10月10日12时12分12秒格式为20091010121212
            'sign' => md5($this->getServiceCode() . $this->getSecretKey() . $now_time),//认证签名，签名规则： MD5（AK+SK+时间戳），其签名值小写
            'datatype' => 'xml',//数据格式 xml json。默认 xml
        ];
        $this->setParams($params);

    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function getServiceCode()
    {
        return $this->serviceCode;
    }

    /**
     * @param string $serviceCode
     */
    public function setServiceCode($serviceCode)
    {
        $this->serviceCode = $serviceCode;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @param string $secretKey
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * @return array
     */
    public function getRequestParamsData()
    {
        return $this->requestParamsData;
    }

    /**
     * @param array $requestParamsData
     */
    public function setRequestParamsData($requestParamsData)
    {
        $this->requestParamsData = array_merge($this->getParams(), $requestParamsData);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getRequestHeadersData()
    {
        return $this->requestHeadersData;
    }

    /**
     * @param array $requestHeadersData
     */
    public function setRequestHeadersData($requestHeadersData)
    {
        $this->requestHeadersData = array_merge($this->getHeaders(), $requestHeadersData);
    }

    /**
     * @return mixed
     */
    public function getCallMethod()
    {
        return $this->callMethod;
    }

    /**
     * @param mixed $callMethod
     */
    public function setCallMethod($callMethod)
    {
        $this->callMethod = $callMethod;
    }

    public function resultHandle($result)
    {
        //返回数据处理
        $return = [
            'code' => 200,
            'msg' => '成功',
            'data' => []
        ];
        //网关服务错误
        if (isset($result['code']) && $result['code'] < 0) {
            $return['code'] = 500;
            $return['msg'] = $result['message'];
            return $return;
        }
        //资源服务错误
        if (isset($result['result']) && $result['result'] != 0) {
            $return['code'] = 500;
            $return['msg'] = $result['errmsg'];
            return $return;
        }
        //响应成功
        $return['data'] = $result;
        return $return;

    }

    /**
     * @return string
     */
    public function getDateTime(): string
    {
        return $this->date_time;
    }

    /**
     * @param string $date_time
     */
    public function setDateTime(string $date_time)
    {
        $this->date_time = $date_time;
    }
}