<?php

namespace TC\Curve\Github;

class Repositories
{
    /**
     * @var \TC\Curve\Github\ApiClient\ApiClient;
     */
    protected static $ApiClient;

    /**
     * @return ApiClient\ApiClient
     */
    protected static function getApiClient(): \TC\Curve\Github\ApiClient\ApiClient
    {
        return self::$ApiClient ?: self::$ApiClient = \TC\Curve\Github\ApiClient\ApiClientFactory::createApiClient();
    }

    /**
     * @param array $repos
     * @return string[];
     */
    protected static function prepareRepos(array $repos): array
    {
        foreach($repos as $k => $repo) {
            if (empty($repo['fork'])) {
                continue;
            }
            if (($res = Repositories::getRepo($repo['full_name'])) && !empty($res['source']['full_name'])) {
                $repos[$k] = $res['source'];
                continue;
            }
        }

        usort(
            $repos,
            function ($a, $b) {
                if ($a['forks'] !== $b['forks']) {
                    // Check most forked first
                    return $b['forks'] - $a['forks'];
                }
                if ($a['id'] !== $b['id']) {
                    // Check most older repos first
                    return $a['id'] - $b['id'];
                }
                return 0;
            }
        );

        // Let's skip repos that have not been forked
        $repos = array_filter(
            $repos,
            function ($repo) {
                return !empty($repo['forks']);
            }
        );

        $result = array_column($repos, 'full_name');
        return $result;
    }

    /**
     * @param User $User
     * @param array $stopUsers
     * @return array
     */
    public static function getConnectedUsersToUser(User $User, array $stopUsers = []): array
    {
        $connectedUsers = [];
        $expectReposCount = $User->getPublicReposCount();
        $reposCount = 0;
        $page = 1;
        do {
            $repos = self::getApiClient()->getUserRepos($User->getLogin(), $page);
            if (empty($repos[0]['full_name'])) {
                break;
            }
            $reposCount += count($repos);
            $repos = self::prepareRepos($repos);

            foreach ($repos as $repo) {
                if (!$res = self::getContributorsByRepo($repo)) {
                    continue;
                }
                $res = array_flip($res);
                if (array_key_exists($User->getLogin(), $res)) {
                    $connectedUsers += $res;
                    if ($stopUsers) {
                        foreach ($stopUsers as $stopUser) {
                            if (array_key_exists($stopUser , $res)) {
                                // It is enough to load
                                break 2;
                            }
                        }
                    }
                }
            }
            $page++;
        } while ($reposCount < $expectReposCount);

        unset($connectedUsers[$User->getLogin()]);
        return $connectedUsers;
    }

    /**
     * @param string $user
     */
    public static function getUser(string $user): array
    {
        $result = self::getApiClient()->getUser($user);
        if (empty($result['login'])) {
            return [];
        }
        return $result;
    }

    /**
     * @param string $repo
     */
    public static function getRepo(string $repo): array
    {
        $result = self::getApiClient()->getRepo($repo);
        if (empty($result['full_name'])) {
            return [];
        }
        return $result;
    }

    /**
     * @param string $repo
     * @return string[]
     */
    public static function getContributorsByRepo($repo): array
    {
        $result = self::getApiClient()->getRepoContributors($repo);
        if (empty($result[0]['login'])) {
            return [];
        }
        return array_map('strtolower', array_column($result, 'login'));
    }
}
