<?php

namespace OAuth2\ClientBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class OAuth2User implements UserInterface, EquatableInterface
{
    private $access_token;
    private $user_id;
    private $scopes;
    private $client_id;
    private $type;
    private $expires;

    public function __construct($access_token, $client_id, $user_id = NULL, \DateTime $expires, array $scopes = array())
    {
        $this->type = ($user_id === NULL) ? 'client' : 'user' ;
        $this->user_id = $user_id;
        $this->password = $client_id;
        $this->scopes = $scopes;
        $this->expires = $expires;
        $this->access_token = $access_token;
    }

    public function getRoles()
    {
        $roles = array();
        foreach ($this->scopes as $scope) {
            $roles[] = 'ROLE_' . strtoupper($scope);
        }
        return $roles;
    }

    public function getPassword()
    {
        return NULL;
    }

    public function getSalt()
    {
        return NULL;
    }

    public function getUsername()
    {
        if ($this->user_id !== NULL) {
            return $this->user_id;
        }
        else {
            return $this->client_id;
        }
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function getScopes()
    {
        return $this->scopes;
    }

    public function getExpires()
    {
        return $this->expires;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function eraseCredentials()
    {
        $this->access_token = NULL;
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof OAuth2User) {
            return false;
        }

        if ($this->client_id !== $user->getClientId()) {
            return false;
        }

        if ($this->user_id !== $user->getUserId()) {
            return false;
        }

        return true;
    }
}