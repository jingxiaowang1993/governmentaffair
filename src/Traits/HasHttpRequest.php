<?php

namespace Government\Affair\Traits;

use Government\Affair\Exceptions\HttpException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

trait HasHttpRequest
{
    /**
     * @param $endpoint
     * @param $query
     * @param $headers
     * @return mixed|string
     * @throws HttpException
     */
    protected function get($endpoint, $query = [], $headers = [])
    {
        return $this->request('get', $endpoint, [
            'headers' => $headers,
            'query' => $query,
        ]);
    }

    /**
     * @param $endpoint
     * @param $params
     * @param $headers
     * @return mixed|string
     * @throws HttpException
     */
    protected function post($endpoint, $params = [], $headers = [])
    {
        return $this->request('post', $endpoint, [
            'headers' => $headers,
            'form_params' => $params,
        ]);
    }

    /**
     * @param $endpoint
     * @param $params
     * @param $headers
     * @return mixed|string
     * @throws HttpException
     */
    protected function postJson($endpoint, $params = [], $headers = [])
    {
        return $this->request('post', $endpoint, [
            'headers' => $headers,
            'json' => $params,
        ]);
    }

    /**
     * @param $method
     * @param $endpoint
     * @param $options
     * @return mixed
     * @throws HttpException
     */
    protected function request($method, $endpoint, $options = [])
    {
        try {
            //初始化
            $client = new Client($this->getBaseOptions());
            //请求
            $response = $client->request($method, $endpoint, $options);
            //响应类型
            $contentType = $response->getHeaderLine('Content-Type');
            //响应内容
            $contents = $response->getBody()->getContents();
            //返回处理
            if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
                return json_decode($contents, true);
            } elseif (false !== stripos($contentType, 'xml')) {
                return json_decode(json_encode(simplexml_load_string($contents)), true);
            }
            //原始返回
            return $contents;
        } catch (TransferException $transferException) {
            throw new HttpException($transferException->getMessage());
        }
    }

    /**
     * @return array
     */
    protected function getBaseOptions()
    {
        return [
            'base_uri' => config('governmentaffair.zjgxfwxt_base_uri'),
            'timeout' => 30,
        ];
    }
}
