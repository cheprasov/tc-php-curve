<?php

namespace Tests\Unit\Controller;

class AbstractControllerTest extends \PHPUnit\Framework\TestCase
{
    public function provider_run()
    {
        $Response = new \TC\Curve\Response\Response();
        $Response->setBody('test');

        return [
            'line ' . __LINE__ => [
                'actionResult' => $Response,
                'expectedResponseBody' => 'test',
            ],
            'line ' . __LINE__ => [
                'actionResult' => 'some text',
                'expectedResponseBody' => 'some text',
            ],
            'line ' . __LINE__ => [
                'actionResult' => 42,
                'expectedResponseBody' => '42',
            ],
            'line ' . __LINE__ => [
                'actionResult' => false,
                'expectedResponseBody' => '',
            ],
            'line ' . __LINE__ => [
                'actionResult' => null,
                'expectedResponseBody' => '',
            ],
        ];
    }

    /**
     * @see \TC\Curve\Controller\AbstractController::run
     * @dataProvider provider_run
     */
    public function test_run($actionResult, $expectedResponseBody)
    {
        $MockedController = $this->getMockForAbstractClass(
            \TC\Curve\Controller\AbstractController::class,
            [],
            '',
            false,
            true,
            true,
            ['action']
        );

        $MockedController
            ->expects($this->once())
            ->method('action')
            ->willReturn($actionResult);

        $Class = new \ReflectionClass(get_class($MockedController));
        $Method = $Class->getMethod('run');
        $Method->setAccessible(true);

        /** @var \TC\Curve\Response\ResponseInterface $Response */
        $Response = $Method->invoke($MockedController, 'action');
        $this->assertTrue($Response instanceof \TC\Curve\Response\ResponseInterface);
        $this->assertSame($expectedResponseBody, $Response->getBody());
    }
}
