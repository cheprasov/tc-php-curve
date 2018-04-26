<?php

namespace TC\Curve\Controller;

abstract class AbstractController implements ControllerInterface
{
    /**
     * @var \TC\Curve\Request\Request
     */
    protected $Request;

    /**
     * @var \TC\Curve\Response\ResponseInterface
     */
    protected $Response;

    /**
     * @param \TC\Curve\Request\Request $Request
     */
    public function __construct(\TC\Curve\Request\Request $Request)
    {
        $this->Request = $Request;
    }

    /**
     * @return \TC\Curve\Response\ResponseInterface
     */
    protected function getResponse()
    {
        return $this->Response ?: $this->Response = new \TC\Curve\Response\Response();
    }

    /**
     * @inheritdoc
     */
    public function run(string $action) : \TC\Curve\Response\ResponseInterface
    {
        $result = $this->$action();
        if ($result instanceof \TC\Curve\Response\ResponseInterface) {
            return $result;
        }
        $Response = $this->getResponse();
        if (isset($result)) {
            $Response->setBody((string)$result);
        }

        return $Response;
    }
}
