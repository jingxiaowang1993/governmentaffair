<?php
return [
    //浙里办APP、支付宝、微信根域名
    'zjgxfwxt_base_uri' => env('ZJGXFWXT_BASE_URI', ''),
    //应用访问授权码（AK）
    'zjgxfwxt_access_key' => env('ZJGXFWXT_ACCESS_KEY', ''),
    //应用访问密钥（SK）
    'zjgxfwxt_secret_key' => env('ZJGXFWXT_SECRET_KEY', ''),
    //应用请求方式
    'zjgxfwxt_call_method' => env('ZJGXFWXT_CALL_METHOD', '1'),
    //微信应用AppId
    'zjgxfwxt_app_id' => env('ZJGXFWXT_APP_ID', ''),
];