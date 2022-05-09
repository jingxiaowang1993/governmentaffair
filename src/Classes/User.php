<?php

namespace Government\Affair\Classes;

use Government\Affair\Exceptions\HttpException;
use Government\Affair\Exceptions\InvalidArgumentException;
use Illuminate\Support\Carbon;

class User extends Base
{
    /**
     * @param $goto
     * @return string
     */
    public function auth($goto)
    {
        //应用key
        $serviceCode = $this->getServiceCode();
        //附带跳转地址，以sp参数返回
        //跳转政务服务进行用户登录
        $redirectUrl = 'https://puser.zjzwfw.gov.cn/sso/mobile.do?action=oauth&scope=1&servicecode=' . $serviceCode . '&goto=' . $goto;
        return $redirectUrl;
    }

    /**
     * @param $ticket
     * @return array
     * @throws InvalidArgumentException
     * @throws HttpException
     */
    public function ticket($ticket)
    {
        if (!$ticket) {
            throw new InvalidArgumentException('Invalid ticket：' . $ticket);
        }

        //基础数据
        $params = [
            'method' => 'ticketValidation',//资源方法
            'st' => $ticket,//票据
        ];
        //设置params
        $this->setRequestParamsData($params);
        $endPoint = '';
        //网关数据
        if ($this->getCallMethod() == 1) {
            $headers = [
                'zjgxfwxt-interface-code' => 'atg.biz.resultful.simpleauth.ticketvalidation',//组件code
            ];
        } else {
            $endPoint = '/restapi/prod/IC33000020220228000002/sso/servlet/simpleauth';
            $headers = $this->getSelfHeaders($endPoint);
        }

        //设置header
        $this->setRequestHeadersData($headers);

        $response = $this->post($endPoint, $this->getRequestParamsData(), $this->getRequestHeadersData());

        return $this->resultHandle($response);
    }

    /**
     * @param $token
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function info($token)
    {
        if (!$token) {
            throw new InvalidArgumentException('Invalid token：' . $token);
        }
        //基础数据
        $params = [
            'method' => 'getUserInfo',//资源方法
            'token' => $token,//票据
        ];
        //设置params
        $this->setRequestParamsData($params);
        $endPoint = '';
        //网关数据
        if ($this->getCallMethod() == 1) {
            $headers = [
                'zjgxfwxt-interface-code' => 'atg.biz.resultful.simpleauth.getuserinfo',//组件code
            ];
        } else {
            $endPoint = '/restapi/prod/IC33000020220228000004/sso/servlet/simpleauth';
            $headers = $this->getSelfHeaders($endPoint);
        }
        //设置header
        $this->setRequestHeadersData($headers);

        $response = $this->post($endPoint, $this->getRequestParamsData(), $this->getRequestHeadersData());

        return $this->resultHandle($response);
    }

    /**
     * @param $endPoint
     * @return array
     */
    public function getSelfHeaders($endPoint)
    {
        $signing_string = 'POST' . "\n" . $endPoint . "\n" . '' . "\n" . $this->getServiceCode() . "\n" . $this->getDateTime() . "\n";
        $signing_string = hash_hmac("sha256", $signing_string, $this->getSecretKey(), true);
        $signing_string = base64_encode($signing_string);

        $headers = [
            'X-BG-HMAC-SIGNATURE' => $signing_string,//API输入参数签名结果
        ];
        return $headers;
    }

}