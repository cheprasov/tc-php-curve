<?php

return [
    // todo: check allowed symbols for name
    '~^(?P<uri>/github/hops/(?P<user1>\w+)/(?P<user2>\w+)/?)$~' => [
        'method' => \TC\Curve\Request\Request::METHOD_GET,
        'controller' => \TC\Curve\Controller\GithubController::class,
        'action' => 'actionGetHops',
    ],
];
