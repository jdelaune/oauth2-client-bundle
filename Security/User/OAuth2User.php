<?php

namespace OAuth2\ClientBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class OAuth2User implements UserInterface, EquatableInterface
{
    private $client_id;
    private $user_id;
    private $scopes;
    private $access_token;

    public function __construct($access_token, $client_id, $user_id = null, array $scopes = array())
    {
        $this->user_id = $user_id;
        $this->client_id = $client_id;
        $this->scopes = $scopes;
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
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        if (!empty($this->user_id)) {
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

    public function isUser()
    {
        if (!empty($this->user_id)) return true;
        return false;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function eraseCredentials()
    {
        // Nothing sensitive held 
        // Do nothing
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof OAuth2User) {
            return false;
        }

        if ($this->access_token !== $user->getAccessToken()) {
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