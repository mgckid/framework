<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/6/7
 * Time: 19:57
 */
return [
    /*应用依赖*/
    'DEPENDENCY_INJECTION_MAP' => [
        'curl' => function ($c) {
            return new \Curl\Curl();
        },
    ],
];