<?php

namespace TC\Curve\Github\ApiClient;

class ApiClient
{
    const URL = 'https://api.github.com/';

    /**
     * @var \TC\Curve\Cache\CacheInterface
     */
    protected $Cache;

    public function __construct(\TC\Curve\Cache\CacheInterface $Cache)
    {
        $this->Cache = $Cache;
    }

    /**
     * @param string $user
     * @param int $page
     * @return array
     */
    public function getUserRepos(string $user, int $page = 1): array
    {
        $page_uri = $page > 1 ? "?page={$page}" : '';
        return $this->request("users/{$user}/repos{$page_uri}");
    }

    /**
     * @param string $repo
     * @return array
     */
    public function getRepoContributors(string $repo): array
    {
        return $this->request("repos/{$repo}/contributors");
    }

    /**
     * @param string $user
     * @return array
     */
    public function getUser(string $user): array
    {
        return $this->request("users/{$user}");
    }

    /**
     * @param string $repo
     * @return array
     */
    public function getRepo(string $repo): array
    {
        return $this->request("repos/{$repo}");
    }

    /**
     * @param string $uri
     * @return array
     * @throws \Exception
     */
    protected function request(string $uri): array
    {
        $cache = $this->Cache->get($uri);
        if ($cache !== null) {
            $output = $cache;
        } else {
            $output = $this->curl(self::URL . $uri);
            $this->Cache->set($uri, $output ?: '');
        }

        if (!$output) {
            return [];
        }

        $result = json_decode($output, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $message = json_last_error_msg();
            throw new \Exception("Cant parse json: {$uri}, msg: '{$message}'");
        }

        return $result ?: [];
    }

    /**
     * I will use here something really simple
     * @param string $url
     * @return string
     * @throws \Exception
     */
    protected function curl(string $url): string
    {
        //todo: repeat request on answer 204
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_USERPWD,
            \TC\Curve\Config\Config::GITHUB_USER . ':' . \TC\Curve\Config\Config::GITHUB_PASS
        );
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'User-Agent: tc-curve-cheprasov',
            ]
        );
        $output = curl_exec($ch);

        if (curl_errno($ch) !== CURLE_OK) {
            $error = curl_error($ch);
            curl_close($ch);
            // todo: create own exceptions for the client
            throw new \Exception("Curl error: {$error}, url: {$url}");
        }
        curl_close($ch);

        return $output ?: '';
    }
}
