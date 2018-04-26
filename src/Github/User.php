<?php

namespace TC\Curve\Github;

class User
{
    /**
     * @var string
     */
    protected $login;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param string $login
     */
    public function __construct(string $login)
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    public function getPublicReposCount()
    {
        return (int)($this->getData()['public_repos'] ?? 0);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (!isset($this->data)) {
            $this->data = Repositories::getUser($this->login) ?: [];
        }
        return $this->data;
    }

}
