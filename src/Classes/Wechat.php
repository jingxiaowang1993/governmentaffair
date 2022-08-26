<?php

namespace Government\Affair\Classes;

use Government\Affair\Exceptions\HttpException;
use Government\Affair\Exceptions\InvalidArgumentException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;

class Wechat extends Base
{
    /**
     * @param $ticket
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public function ticket($ticket)
    {
        if (!$ticket) {
            throw new InvalidArgumentException('Invalid ticket：' . $ticket);
        }

        //基础数据
        $params = [
            'appId' => config('governmentaffair.zjgxfwxt_app_id'),//AppId
            'ticketId' => $ticket,//票据
        ];
        //设置params
        $this->setRequestParamsData($params);
        $endPoint = '/restapi/prod/IC33000020220329000007/uc/sso/access_token';
        $headers = $this->getSelfHeaders($endPoint);

        //设置header
        $this->setRequestHeadersData($headers);

        $response = $this->postJson($endPoint, $this->getRequestParamsData(), $this->getRequestHeadersData());

        return $this->resultHandleWechat($response);
    }

    /**
     * @param $token
     * @return array
     * @throws GuzzleException
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
            'token' => $token,//token
        ];
        //设置params
        $this->setRequestParamsData($params);
        $endPoint = '/restapi/prod/IC33000020220329000008/uc/sso/getUserInfo';
        $headers = $this->getSelfHeaders($endPoint);
        //设置header
        $this->setRequestHeadersData($headers);

        $response = $this->postJson($endPoint, $this->getRequestParamsData(), $this->getRequestHeadersData());

        return $this->resultHandleWechat($response);
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