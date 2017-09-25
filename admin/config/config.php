<?php
return array(
    'DEPENDENCY_INJECTION' => [
        /*会话依赖*/
        'session' => function ($c) {
            return (new \Aura\Session\SessionFactory())->newInstance($_COOKIE);
        },
        /*会话依赖*/
        'segment' => function ($c) {
            $session = $c['session'];
            $session->setCookieParams(array('lifetime' => 1800 * 24));
            $segment_key = $c['config']->get('SEGMENT_KEY');
            return $session->getSegment($segment_key);
        }
    ],
);