<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/6/7
 * Time: 19:57
 */
return [
    'DEPENDENCY_INJECTION' => [
        'curl' => function ($c) {
            return new \Curl\Curl();
        },
    ],
];