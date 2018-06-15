<?php
/**
 * Created by PhpStorm.
 * Author: ChenHua <Http://www.ichenhua.cn>
 * Date: 2018/6/15 11:31
 */

return [
    "default"     => 'local', //默认返回存储位置url
    "dirver"      => ['local'], //存储平台
    "connections" => [
        "local"  => [
            'prefix' => 'uploads/kindeditor',
        ],
        "qiniu"  => [
            'access_key' => '',
            'secret_key' => '',
            'bucket'     => '',
            'prefix'     => '',
            'domain'     => ''
        ],
        "aliyun" => [
            'ak_id'     => '',
            'ak_secret' => '',
            'end_point' => '',
            'bucket'    => '',
            'prefix'    => '',
        ],
    ],
];
