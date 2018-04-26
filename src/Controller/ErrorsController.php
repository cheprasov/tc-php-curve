<?php

namespace TC\Curve\Controller;

class ErrorsController extends AbstractController
{
    public function actionNotFound()
    {
        $Response = $this->getResponse();
        $Response->setCode(\TC\Curve\Response\ResponseInterface::CODE_NOT_FOUND);
        $Response->setBody('404 Not Found');
    }
}
