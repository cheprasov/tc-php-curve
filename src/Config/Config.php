<?php

namespace TC\Curve\Config;

// todo: separate config by platforms
class Config
{
    const GITHUB_USER = 'cheprasov';

    const GITHUB_PASS = '02356d4774e0f2e1a8450ce218c14c81b44fb103';

    /**
     * @var array|null
     */
    protected static $routes;

    public static function init()
    {
        ini_set('display_errors', true);
        ini_set('max_execution_time', 5);

        define('APP_CACHE_DIR', __DIR__ . '/../../cache/');
    }

    /**
     * @return array
     */
    public static function getRoutes(): array
    {
        return self::$routes ?: self::$routes = include(__DIR__ . '/routes.php');
    }
}
