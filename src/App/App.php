<?php

namespace TC\Curve\App;

class App
{
    /** @var  \TC\Curve\Request\Request */
    protected $Request;

    /**
     * @return \TC\Curve\Request\Request
     */
    protected function getRequest()
    {
        return $this->Request ?: $this->Request = new \TC\Curve\Request\Request();
    }

    public function run()
    {
        try {
            if ($route = $this->getRoute()) {
                $Controller = new $route['controller']($this->getRequest());
                $action = $route['action'];
            } else {
                $Controller = new \TC\Curve\Controller\ErrorsController($this->getRequest());
                $action = 'actionNotFound';
            }
            /** @var \TC\Curve\Controller\ControllerInterface $Controller */
            $Response = $Controller->run($action);
            $this->echoResponse($Response);
        } catch (\Exception $E) {
            // Some handlers for exceptions
            echo "Error {$E->getCode()}: {$E->getMessage()}";
        }
    }

    /**
     * @param \TC\Curve\Response\ResponseInterface $Response
     */
    protected function echoResponse(\TC\Curve\Response\ResponseInterface $Response)
    {
        http_response_code($Response->getCode());
        foreach ($Response->getHeaders() as $key => $value) {
            header("{$key}:{$value}", true);
        }
        echo $Response->getBody();
    }

    /**
     * @return null|array
     */
    protected function getRoute()
    {
        $Request = $this->getRequest();
        $uri = $Request->getUri();
        $method = $Request->getMethod();

        foreach (\TC\Curve\Config\Config::getRoutes() as $regexp => $route) {
            if (empty($route['method'])) {
                continue;
            }
            if (is_array($route['method'])) {
                if (!in_array($method, $route['method'])) {
                    continue;
                }
            } elseif ($route['method'] !== $method) {
                continue;
            }
            if (!preg_match($regexp, $uri, $matches)) {
                continue;
            }
            $Request->setRouteVars($matches);
            return $route;
        }
        return null;
    }

}
