<?php

namespace Tests\Unit\App;

class AppTest extends \PHPUnit\Framework\TestCase
{
    public function provider_getRoute()
    {
        $expectRoute = [
            'method' => 'GET',
            'controller' => 'TC\Curve\Controller\GithubController',
            'action' => 'actionGetHops',
        ];

        return [
            'line ' . __LINE__ => [
                'requestUri' => '/github/hops/cheprasov/test/',
                'requestMethod' => 'GET',
                'expectRoute' => $expectRoute,
                'expectVars' => [
                    'user1' => 'cheprasov',
                    'user2' => 'test',
                ],
            ],
            'line ' . __LINE__ => [
                'requestUri' => '/github/hops/Seldaek/stof/',
                'requestMethod' => 'GET',
                'expect' => $expectRoute,
                'expectVars' => [
                    'user1' => 'Seldaek',
                    'user2' => 'stof',
                ],
            ],
            'line ' . __LINE__ => [
                'requestUri' => '/github/hops/Seldaek/stof/',
                'requestMethod' => 'POST',
                'expect' => null,
                'expectVars' => null,
            ],
            'line ' . __LINE__ => [
                'requestUri' => '/github/hops/saeldak/and/stof/',
                'requestMethod' => 'GET',
                'expect' => null,
                'expectVars' => null,
            ],
            'line ' .  __LINE__ => [
                'requestUri' => '/github/also_wrong_uri/',
                'requestMethod' => 'GET',
                'expect' => null,
                'expectVars' => null,
            ],
        ];
    }

    /**
     * @see \TC\Curve\App\App::getRoute
     * @dataProvider provider_getRoute
     */
    public function test_getRoute($requestUri, $requestMethod, $expectRoute, $expectVars)
    {
        $App = new \TC\Curve\App\App();

        $Class = new \ReflectionClass(get_class($App));
        $Method = $Class->getMethod('getRoute');
        $Method->setAccessible(true);

        /** @var \TC\Curve\Request\Request|\PHPUnit\Framework\MockObject\MockObject $RequestMock */
        $RequestMock = $this->getMockBuilder(\TC\Curve\Request\Request::class)
            ->setMethods(['getUri', 'getMethod'])
            ->getMock();

        $RequestMock->expects($this->once())->method('getUri')->willReturn($requestUri);
        $RequestMock->expects($this->once())->method('getMethod')->willReturn($requestMethod);
        $Property = $Class->getProperty('Request');
        $Property->setAccessible(true);
        $Property->setValue($App, $RequestMock);

        $result = $Method->invoke($App, 'getRoute');
        $this->assertSame($expectRoute, $result);

        if ($expectVars) {
            $vars = $RequestMock->getRouteVars();
            foreach ($expectVars as $k => $v) {
                $this->assertTrue(isset($vars[$k]));
                $this->assertSame($v, $vars[$k]);
            }
        } else {
            $this->assertSame(null, $RequestMock->getRouteVars());
        }
    }
}
