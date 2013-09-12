<?php

namespace OAuth2\ClientBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OAuth2\ClientBundle\Security\Authentication\Token\OAuth2AccessToken;

class OAuth2Provider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $cacheDir;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function authenticate(TokenInterface $token)
    {
        try {
            $user = $this->userProvider->loadUserByAccessToken($token->getAccessToken());

            $authenticatedToken = new OAuth2AccessToken($user->getRoles());
            $authenticatedToken->setUser($user);

            return $authenticatedToken;
        }
        catch (\Exception $e)
        {
            throw new AuthenticationException('The OAuth2 Access Token is invalid.');
        }

        throw new AuthenticationException('The OAuth2 authentication failed.');
    }

    protected function validateAccessToken($access_token)
    {
        return FALSE;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof OAuth2AccessToken;
    }
}