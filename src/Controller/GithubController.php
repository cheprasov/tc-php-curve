<?php

namespace TC\Curve\Controller;

class GithubController extends AbstractController
{
    public function actionGetHops()
    {
        $vars = $this->Request->getRouteVars();
        $HopsFinder = new \TC\Curve\Github\HopsFinder($vars['user1'], $vars['user2']);
        return $HopsFinder->getHopsCount();
    }
}
