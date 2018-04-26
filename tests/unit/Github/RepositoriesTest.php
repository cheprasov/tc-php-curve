<?php

namespace Tests\Unit\Github;

class RepositoriesTest extends \PHPUnit\Framework\TestCase
{
    public function provider_prepareRepos()
    {
        return [
            'line ' . __LINE__ => [
                'repos' => [],
                'expect' => [],
            ],
            'line ' . __LINE__ => [
                'repos' => [
                    [
                        'id' => 100,
                        'full_name' => 'Seldaek/monolog',
                        'fork' => false,
                        'forks' => 42,
                    ],
                    [
                        'id' => 200,
                        'full_name' => 'cheprasov/test',
                        'fork' => false,
                        'forks' => 10,
                    ],
                    [
                        'id' => 300,
                        'full_name' => 'foo/bar',
                        'fork' => false,
                        'forks' => 11,
                    ],
                    [
                        'id' => 350,
                        'full_name' => 'skip/me',
                        'fork' => false,
                        'forks' => 0,
                    ],
                    [
                        'id' => 400,
                        'full_name' => 'fork_html/css',
                        'fork' => true,
                        'forks' => 3,
                    ],
                    [
                        'id' => 99,
                        'full_name' => 'hello/world',
                        'fork' => false,
                        'forks' => 42,
                    ],
                ],
                'expect' => [
                    'hello/world',
                    'Seldaek/monolog',
                    'foo/bar',
                    'cheprasov/test',
                    'html/css',
                ],
            ],
        ];
    }

    /**
     * @see \TC\Curve\Github\Repositories::prepareRepos
     * @dataProvider provider_prepareRepos
     */
    public function test_prepareRepos($repos, $expect)
    {
        $ApiClientMock = $this->getMockBuilder(\TC\Curve\Github\ApiClient\ApiClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepo'])
            ->getMock();
        $ApiClientMock->expects($this->any())->method('getRepo')
            ->willReturn(
                [
                    'full_name' => 'fork_html/css',
                    'source' => [
                        'id' => 401,
                        'full_name' => 'html/css',
                        'fork' => false,
                        'forks' => 3,
                    ],
                ]
            );

        $Class = new \ReflectionClass(\TC\Curve\Github\Repositories::class);
        $Property = $Class->getProperty('ApiClient');
        $Property->setAccessible(true);
        $Property->setValue($ApiClientMock);

        $Method = $Class->getMethod('prepareRepos');
        $Method->setAccessible(true);
        $result = $Method->invoke(null, $repos);

        $this->assertSame($expect, $result);
    }

    public function provider_getConnectedUsersToUser()
    {
        return [
            'line ' . __LINE__ => [
                'repos' => [
                    [
                        'id' => 100,
                        'full_name' => 'Seldaek/monolog',
                        'fork' => false,
                        'forks' => 42,
                    ],
                    [
                        'id' => 200,
                        'full_name' => 'cheprasov/test',
                        'fork' => false,
                        'forks' => 12,
                    ],
                    [
                        'id' => 300,
                        'full_name' => 'foo/bar',
                        'fork' => false,
                        'forks' => 11,
                    ],
                    [
                        'id' => 350,
                        'full_name' => 'Some/thing',
                        'fork' => false,
                        'forks' => 0,
                    ],
                    [
                        'id' => 351,
                        'full_name' => 'Any/thing',
                        'fork' => false,
                        'forks' => 10,
                    ],
                ],
                'conts' => [
                    'Seldaek/monolog' => [
                        ['login' => 'Seldaek'],
                        ['login' => 'Stof'],
                        ['login' => 'Foo'],
                        ['login' => 'Bar'],
                    ],
                    'cheprasov/test' => [
                        ['login' => 'Cheprasov'],
                        ['login' => 'Curve'],
                        ['login' => 'Tc'],
                    ],
                    'foo/bar' => [
                        ['login' => 'Cheprasov'],
                        ['login' => 'Foo'],
                        ['login' => 'Bar'],
                        ['login' => 'StopUser'],
                        ['login' => 'SomeBody'],
                    ],
                    'Some/thing' => [
                        ['login' => 'Should'],
                        ['login' => 'Be'],
                        ['login' => 'Skipped'],
                    ],
                    'Any/thing' => [
                        ['login' => 'Should'],
                        ['login' => 'Be'],
                        ['login' => 'Skipped'],
                        ['login' => 'Too'],
                    ]
                ],
                'expect' => [
                    'curve', 'tc', 'foo', 'bar', 'stopuser', 'somebody'
                ],
            ],
        ];
    }

    /**
     * @see \TC\Curve\Github\Repositories::getConnectedUsersToUser
     * @dataProvider provider_getConnectedUsersToUser
     */
    public function test_getConnectedUsersToUser($repos, $conts, $expect)
    {
        $User = new \TC\Curve\Github\User('cheprasov');
        $Class = new \ReflectionClass(get_class($User));
        $Property = $Class->getProperty('data');
        $Property->setAccessible(true);
        $Property->setValue($User, ['public_repos' => count($repos)]);

        $ApiClientMock = $this->getMockBuilder(\TC\Curve\Github\ApiClient\ApiClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserRepos', 'getRepoContributors'])
            ->getMock();

        $ApiClientMock->expects($this->any())->method('getUserRepos')
            ->willReturnCallback(
                function() use ($repos) {
                    static $r;
                    if (!isset($r)) {
                        $r = array_chunk($repos, 2);
                    }
                    return array_shift($r) ?: [];
                }
            );
        $ApiClientMock->expects($this->any())->method('getRepoContributors')
            ->willReturnCallback(
                function($repo) use ($conts) {
                    return $conts[$repo] ?? [];
                }
            );

        $Class = new \ReflectionClass(\TC\Curve\Github\Repositories::class);
        $Property = $Class->getProperty('ApiClient');
        $Property->setAccessible(true);
        $Property->setValue($ApiClientMock);

        $Method = $Class->getMethod('getConnectedUsersToUser');
        $Method->setAccessible(true);
        $result = $Method->invoke(null, $User, ['stopUser']);

        $this->assertSame($expect, array_keys($result));
    }
}
