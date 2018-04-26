<?php

namespace TC\Curve\Github;

class HopsFinder
{
    const MAX_HOPS_LIMIT = 42; // use INF for Infinity

    /**
     * @var string
     */
    protected $user1;

    /**
     * @var string
     */
    protected $user2;

    /**
     * @param string $user1
     * @param string $user2
     */
    public function __construct(string $user1, string $user2)
    {
        $this->user1 = strtolower($user1);
        $this->user2 = strtolower($user2);
    }

    /**
     * @return int
     */
    public function getHopsCount(): int
    {
        if ($this->user1 === $this->user2) {
            return 0;
        }

        $User1 = \TC\Curve\Github\UserFactory::createUser($this->user1);
        $User2 = \TC\Curve\Github\UserFactory::createUser($this->user2);

        if (!$User1->getPublicReposCount() || !$User2->getPublicReposCount()) {
            // todo:
            // One of users has not public repos,
            // but maybe they contributed into repos, that deleted from their page
            // I think, I will not check it, because it just a test
            return -1;
        }

        if ($User1->getPublicReposCount() > $User2->getPublicReposCount()) {
            // I think, it is better to start from user, who has less public repos
            list($User1, $User2) = [$User2, $User1];
        }

        return $this->findMutualUsers([$User1->getLogin() => 0], [$User2->getLogin() => 0]);
    }

    /**
     * @param array $arr1
     * @param array $arr2
     */
    protected function isIntersect(array $arr1, array $arr2): bool
    {
        if (!$arr1 || !$arr2) {
            return false;
        }
        foreach ($arr1 as $k => $v) {
            if (array_key_exists($k, $arr2)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $users1
     * @param array $users2
     * @return int
     */
    protected function findMutualUsers(array $users1, array $users2): int
    {
        $users = [$users1, $users2];
        $hopsCount = 1;
        $checkedUsers = [];
        while ($hopsCount <= self::MAX_HOPS_LIMIT) {
            for ($i = 0; $i < 2; $i++) {
                $searchUsers = $users[$i];
                $stopUsers = $users[abs($i - 1)];

                $usersOfUser = [];
                foreach ($searchUsers as $u => $v) {
                    if (!empty($checkedUsers[$u])) {
                        continue;
                    }
                    $User = \TC\Curve\Github\UserFactory::createUser($u);
                    $part = $this->getConnectedUsersToUser($User, $stopUsers);

                    if ($this->isIntersect($part, $stopUsers)) {
                        return $hopsCount;
                    }
                    $usersOfUser += $part;
                    $checkedUsers[$u] = true;
                }
                if (!$usersOfUser) {
                    return -1;
                }
                $users[$i] = $usersOfUser;
                $hopsCount++;
            }
        }

        return -1;
    }

    /**
     * @param User $User
     * @param string[] $stopUsers
     * @return mixed
     */
    protected function getConnectedUsersToUser(User $User, array $stopUsers = []): array
    {
        return \TC\Curve\Github\Repositories::getConnectedUsersToUser($User, array_keys($stopUsers));
    }
}
