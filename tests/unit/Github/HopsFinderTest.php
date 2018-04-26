<?php

namespace Tests\Unit\Github;

class HobsFinderTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        parent::setUp();
        $Class = new \ReflectionClass(\TC\Curve\Github\UserFactory::class);
        $Property = $Class->getProperty('Users');
        $Property->setAccessible(true);
        $Property->setValue(null, []);
    }

    public function provider_isIntersect()
    {
        return [
            'line ' . __LINE__ => [
                'arr1' => [],
                'arr2' => [],
                'expect' => false
            ],
            'line ' . __LINE__ => [
                'arr1' => ['a', 'b', 'c'],
                'arr2' => [],
                'expect' => false
            ],
            'line ' . __LINE__ => [
                'arr1' => [],
                'arr2' => ['d', 'e', 'f'],
                'expect' => false
            ],
            'line ' . __LINE__ => [
                'arr1' => ['a', 'b', 'c'],
                'arr2' => ['d', 'e', 'f'],
                'expect' => false
            ],
            'line ' . __LINE__ => [
                'arr1' => ['a', 'b', 'c'],
                'arr2' => ['a'],
                'expect' => true
            ],
            'line ' . __LINE__ => [
                'arr1' => ['a', 'b', 'c'],
                'arr2' => ['e', 'c'],
                'expect' => true
            ],
            'line ' . __LINE__ => [
                'arr1' => ['a'],
                'arr2' => ['a'],
                'expect' => true
            ],
            'line ' . __LINE__ => [
                'arr1' => ['a'],
                'arr2' => ['a', 'b', 'c'],
                'expect' => true
            ],
        ];
    }
    /**
     * @see \TC\Curve\Github\HopsFinder::isIntersect
     * @dataProvider provider_isIntersect
     */
    public function test_isIntersect($arr1, $arr2, $expect)
    {
        /** @var \TC\Curve\Github\HopsFinder|\PHPUnit\Framework\MockObject\MockObject $FinderMock */
        $FinderMock = $this->getMockBuilder(\TC\Curve\Github\HopsFinder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $Class = new \ReflectionClass(get_class($FinderMock));
        $Method = $Class->getMethod('isIntersect');
        $Method->setAccessible(true);

        $result = $Method->invoke($FinderMock, array_flip($arr1), array_flip($arr2));
        $this->assertSame($expect, $result);
    }

    /**
     * @param string $name
     * @param array $data
     * @return \TC\Curve\Github\User
     */
    protected function createMockedUser($name, $data): \TC\Curve\Github\User
    {
        $User = \TC\Curve\Github\UserFactory::createUser($name);
        $Class = new \ReflectionClass(get_class($User));
        $Property = $Class->getProperty('data');
        $Property->setAccessible(true);
        $Property->setValue($User, $data);
        return $User;
    }

    public function provider_getHopsCount()
    {
        return [
            'line ' . __LINE__ => [
                'user1' => 'cheprasov',
                'user2' => 'CHEPRASOV',
                'userData1' => ['public_repos' => 0],
                'userData2' => ['public_repos' => 0],
                'expect' => 0,
            ],
            'line ' . __LINE__ => [
                'user1' => 'Seldaek',
                'user2' => 'Stof',
                'userData1' => ['public_repos' => 1],
                'userData2' => ['public_repos' => 0],
                'expect' => -1,
            ],
            'line ' . __LINE__ => [
                'user1' => 'Seldaek',
                'user2' => 'Stof',
                'userData1' => ['public_repos' => 0],
                'userData2' => ['public_repos' => 1],
                'expect' => -1,
            ],
            'line ' . __LINE__ => [
                'user1' => 'Seldaek',
                'user2' => 'Stof',
                'userData1' => ['public_repos' => 1],
                'userData2' => ['public_repos' => 1],
                'expect' => 2,
            ],
        ];
    }

    /**
     * @see \TC\Curve\Github\HopsFinder::getHopsCount
     * @dataProvider provider_getHopsCount
     */
    public function test_getHopsCount($user1, $user2, $userData1, $userData2, $expect)
    {
        $this->createMockedUser($user1, $userData1);
        $this->createMockedUser($user2, $userData2);

        /** @var \TC\Curve\Github\HopsFinder|\PHPUnit\Framework\MockObject\MockObject $FinderMock */
        $FinderMock = $this->getMockBuilder(\TC\Curve\Github\HopsFinder::class)
            ->setConstructorArgs([$user1, $user2])
            ->setMethods(['findMutualUsers'])
            ->getMock();

        $FinderMock->expects($this->any())->method('findMutualUsers')->willReturn(2);

        $result = $FinderMock->getHopsCount();
        $this->assertSame($expect, $result);
    }

    public function provider_findMutualUsers()
    {
        return [
            'line ' . __LINE__ => [
                'user1' => 'cheprasov',
                'user2' => 'test',
                'connectedUsers' => [],
                'expect' => -1,
            ],
            'line ' . __LINE__ => [
                'user1' => 'cheprasov',
                'user2' => 'test',
                'connectedUsers' => [
                    'cheprasov' => ['aa', 'bb', 'cc'],
                    'cc' => ['dd', 'ee', 'cheprasov'],
                    'dd' => ['cc', 'ee', 'ff'],
                    'ff' => ['dd', 'test'],
                    'test' => ['ff'],
                ],
                'expect' => 4,
            ],
            'line ' . __LINE__ => [
                'user1' => 'seldaek',
                'user2' => 'stof',
                'connectedUsers' => [
                    'seldaek' => ['aa', 'bb', 'stof'],
                    'stof' => ['dd', 'ee', 'seldaek'],
                ],
                'expect' => 1,
            ],
            'line ' . __LINE__ => [
                'user1' => 'aa',
                'user2' => 'zz',
                'connectedUsers' => [
                    'aa' => ['bb', 'cc', 'zz'],
                    'bb' => ['cc', 'aa', 'zz'],
                    'cc' => ['bb', 'aa', 'zz'],
                    'zz' => ['bb', 'cc', 'aa'],
                ],
                'expect' => 1,
            ],
            'line ' . __LINE__ => [
                'user1' => 'aa',
                'user2' => 'zz',
                'connectedUsers' => [
                    'aa' => ['bb', 'cc'],
                    'bb' => ['cc', 'aa', 'zz'],
                    'cc' => ['bb', 'aa', 'zz'],
                    'zz' => ['bb', 'cc'],
                ],
                'expect' => 2,
            ],
            'line ' . __LINE__ => [
                'user1' => 'aa',
                'user2' => 'zz',
                'connectedUsers' => [
                    'aa' => ['bb', 'cc'],
                    'bb' => ['cc', 'aa', 'ee'],
                    'cc' => ['bb', 'aa', 'ee'],
                    'ee' => ['bb', 'cc', 'zz'],
                    'zz' => ['ee'],
                ],
                'expect' => 3,
            ],
            'line ' . __LINE__ => [
                'user1' => 'aa',
                'user2' => 'zz',
                'connectedUsers' => [
                    'aa' => ['bb', 'cc'],
                    'bb' => ['cc', 'aa', 'ee'],
                    'cc' => ['bb', 'aa', 'ee'],
                    'ee' => ['bb', 'cc', 'ff'],
                    'ff' => ['ee', 'zz'],
                    'zz' => ['ff', 'xx', 'yy'],
                ],
                'expect' => 4,
            ],
            'line ' . __LINE__ => [
                'user1' => 'aa',
                'user2' => 'zz',
                'connectedUsers' => [
                    'aa' => ['bb', 'cc'],
                    'bb' => ['cc', 'aa', 'ee'],
                    'cc' => ['bb', 'aa', 'ee'],
                    'ee' => ['bb', 'cc', 'ff'],
                    'ff' => ['ee', 'gg'],
                    'gg' => ['ff', 'zz'],
                    'zz' => ['gg', 'xx', 'yy'],
                ],
                'expect' => 5,
            ],
            'line ' . __LINE__ => [
                'user1' => 'aa',
                'user2' => 'zz',
                'connectedUsers' => [
                    'aa' => ['bb', 'cc'],
                    'bb' => ['cc', 'aa', 'ee'],
                    'cc' => ['bb', 'aa', 'ee'],
                    'ee' => ['bb', 'cc', 'ff'],
                    'ff' => ['ee', 'gg'],
                    'gg' => ['ff', 'hh'],
                    'hh' => ['gg', 'zz'],
                    'zz' => ['hh', 'xx', 'yy'],
                ],
                'expect' => 6,
            ],
            'line ' . __LINE__ => [
                'user1' => 'aa',
                'user2' => 'zz',
                'connectedUsers' => [
                    'aa' => ['bb', 'cc'],
                    'bb' => ['cc', 'aa', 'ee'],
                    'cc' => ['bb', 'aa', 'ee'],
                    'ee' => ['bb', 'cc', 'ff'],
                    'ff' => ['ee', 'gg'],
                    'gg' => ['ff', 'hh'],
                    'hh' => ['gg', 'ii'],
                    'ii' => ['hh', 'zz', 'foo', 'bar'],
                    'zz' => ['ii', 'xx', 'yy'],
                ],
                'expect' => 7,
            ],
            'line ' . __LINE__ => [
                'user1' => 'aa',
                'user2' => 'zz',
                'connectedUsers' => [
                    'aa' => ['bb', 'cc'],
                    'bb' => ['cc', 'aa', 'ee'],
                    'cc' => ['bb', 'aa', 'ee'],
                    'ee' => ['bb', 'cc', 'ff'],
                    'ff' => ['ee', 'gg'],
                    'gg' => ['ff', 'hh'],
                    'hh' => ['gg', 'ii'],
                    'ii' => ['hh', 'jj', 'foo', 'bar'],
                    'jj' => ['ii', 'zz', 'foo', 'bar'],
                    'zz' => ['xx', 'yy', 'jj'],
                ],
                'expect' => 8,
            ],
            'line ' . __LINE__ => [
                'user1' => 'aa',
                'user2' => 'zz',
                'connectedUsers' => [
                    'aa' => ['bb', 'cc', 'dd', 'ee', 'ff', 'gg', 'hh'],
                    'hh' => ['hh', 'ii', 'jj', 'kk', 'll', 'mm', 'zz'],
                    'zz' => ['hh', 'oo', 'pp', 'qq', 'ss', 'tt', 'uu'],
                ],
                'expect' => 2,
            ],
        ];
    }

    /**
     * @see \TC\Curve\Github\HopsFinder::findMutualUsers
     * @dataProvider provider_findMutualUsers
     */
    public function test_findMutualUsers($user1, $user2, $connectedUsers, $expect)
    {
        /** @var \TC\Curve\Github\HopsFinder|\PHPUnit\Framework\MockObject\MockObject $FinderMock */
        $FinderMock = $this->getMockBuilder(\TC\Curve\Github\HopsFinder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getConnectedUsersToUser'])
            ->getMock();

        $FinderMock->expects($this->any())->method('getConnectedUsersToUser')
            ->willReturnCallback(
                function(\TC\Curve\Github\User $User, $stopUsers) use ($connectedUsers) {
                    $res = $connectedUsers[$User->getLogin()] ?? [];
                    return array_flip($res);
                }
            );

        $Class = new \ReflectionClass(get_class($FinderMock));
        $Method = $Class->getMethod('findMutualUsers');
        $Method->setAccessible(true);

        $result = $Method->invoke($FinderMock, [$user1 => 1], [$user2 => 1]);
        $this->assertSame($expect, $result);
    }
}
