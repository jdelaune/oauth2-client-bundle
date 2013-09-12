<?php

namespace OAuth2\ClientBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class OAuth2AccessToken extends AbstractToken
{
    private $access_token;

    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function getCredentials()
    {
        return '';
    }
}
