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


    public function __construct()
    {
        //应用访问授权码（AK）
        $this->setServiceCode(config('governmentaffair.zjgxfwxt_access_key'));
        //应用访问密钥（SK）
        $this->setSecretKey(config('governmentaffair.zjgxfwxt_secret_key'));
        //网关服务
        $this->gateway();
    }

    public function gateway()
    {
        // TODO: Implement gateway() method.
        //获取毫秒时间戳
        $time = Carbon::now()->getTimestampMs();
        $headers = [
            'zjgxfwxt-access-key' => $this->getServiceCode(),
            'zjgxfwxt-sign' => md5($this->getServiceCode() . $this->getSecretKey() . $time),//认证签名，签名规则： MD5（AK+SK+时间毫秒值），其签名值小写
            'zjgxfwxt-time' => $time//时间毫秒值（示例：1632643914516）
        ];
        $this->setHeaders($headers);
        $now_time = date("YmdHis", time());
        $params = [
            'servicecode' => $this->getServiceCode(),//应用访问授权码（AK）
            'time' => $now_time,//时间戳，当前时间年月日时分秒例：2009年10月10日12时12分12秒格式为20091010121212
            'sign' => md5($this->getServiceCode() . $this->getSecretKey() . $now_time),//认证签名，签名规则： MD5（AK+SK+时间戳），其签名值小写
            'datatype' => 'json',//数据格式 xml json。默认 xml
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

    public function resultHandle($result)
    {
        //返回数据处理
        $return = [
            'code' => 200,
            'msg' => '成功',
            'data' => []
        ];
        //正常响应数据(HttpStatus=200)
        $result_data = $result['data'];
        //网关服务错误
        if ($result['code'] < 0) {
            $return['code'] = 500;
            $return['msg'] = $result['message'];
            return $return;
        }
        //资源服务错误
        if ($result_data['result'] != 0) {
            $return['code'] = 500;
            $return['msg'] = $result_data['errmsg'];
            return $return;
        }
        //响应成功
        $return['data'] = $result_data;
        return $return;

    }
}