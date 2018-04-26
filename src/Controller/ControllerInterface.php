<?php

namespace TC\Curve\Controller;

interface ControllerInterface
{
    /**
     * @param string $action
     * @return \TC\Curve\Response\ResponseInterface
     */
    public function run(string $action): \TC\Curve\Response\ResponseInterface;
}
