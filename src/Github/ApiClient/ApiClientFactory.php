<?php

namespace TC\Curve\Github\ApiClient;

class ApiClientFactory
{
    /**
     * @return ApiClient
     */
    public static function createApiClient(): ApiClient
    {
        return new ApiClient(new \TC\Curve\Cache\FileCache());
    }
}
