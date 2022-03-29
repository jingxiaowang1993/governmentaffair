<?php

namespace Government\Affair\Classes;

use Government\Affair\Exceptions\InvalidArgumentException;

class User extends Base
{
    /**
     * @param $goto
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function auth($goto)
    {
        //应用key
        $serviceCode = $this->getServiceCode();
        //附带跳转地址，以sp参数返回
        //跳转政务服务进行用户登录
        $redirectUrl = 'https://puser.zjzwfw.gov.cn/sso/mobile.do?action=oauth&scope=1&servicecode=' . $serviceCode . '&goto=' . $goto;
        return redirect($redirectUrl);
    }

    /**
     * @param $ticket
     * @return array
     * @throws InvalidArgumentException
     */
    public function ticket($ticket): array
    {
        if (!$ticket) {
            throw new InvalidArgumentException('Invalid ticket：' . $ticket);
        }
        //网关数据
        $headers = [
            'zjgxfwxt-interface-code' => 'atg.biz.resultful.simpleauth.ticketvalidation',//组件code
        ];
        //设置header
        $this->setRequestHeadersData($headers);
        //基础数据
        $params = [
            'method' => 'ticketValidation',//资源方法
            'st' => $ticket,//票据
        ];
        //设置params
        $this->setRequestParamsData($params);

        $response = $this->postJson('', $this->getRequestParamsData(), $this->getRequestHeadersData());

        return $this->resultHandle($response);
    }

    /**
     * @param $token
     * @return array
     * @throws InvalidArgumentException
     */
    public function info($token): array
    {
        if (!$token) {
            throw new InvalidArgumentException('Invalid token：' . $token);
        }
        //网关数据
        $headers = [
            'zjgxfwxt-interface-code' => 'atg.biz.resultful.simpleauth.getuserinfo',//组件code
        ];
        //设置header
        $this->setRequestHeadersData($headers);
        //基础数据
        $params = [
            'method' => 'getUserInfo',//资源方法
            'token' => $token,//票据
        ];
        //设置params
        $this->setRequestParamsData($params);

        $response = $this->postJson('', $this->getRequestParamsData(), $this->getRequestHeadersData());

        return $this->resultHandle($response);
    }
}