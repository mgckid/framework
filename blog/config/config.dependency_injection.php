<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/9/25
 * Time: 10:24
 */
return [
    'DEPENDENCY_INJECTION' => [
        'curl' => function ($c) {
            $curl = new \Curl\Curl();
            return $curl;
        },
    ],
];