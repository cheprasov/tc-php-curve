<?php

namespace TC\Curve\Github;

class UserFactory
{
    /**
     * @var User[]
     */
    static protected $Users = [];

    /**
     * @param string $user
     * @return User
     */
    public static function createUser(string $user): User
    {
        $user = strtolower($user);
        if (!isset(static::$Users[$user])) {
            static::$Users[$user] = new User($user);
        }
        return static::$Users[$user];
    }
}
