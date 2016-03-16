<?php

namespace OAuth2\ClientBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use GuzzleHttp\Client;

class OAuth2UserProvider implements UserProviderInterface
{
    protected $serverVerifyUri;
    protected $validateSSL;

    public function __construct($oauth2_server)
    {
        $this->serverVerifyUri = $oauth2_server['verify_uri'];
        $this->validateSSL = $oauth2_server['validate_ssl'];
    }

    public function loadUserByUsername($username)
    {
        return $this->loadUserByAccessToken($username);
    }

    public function loadUserByAccessToken($access_token)
    {
        // Verify Access Token and get details back
        $client = new Client();
        if ($this->validateSSL === false) {
            $client = new Client(['ssl.certificate_authority' => false]);
        }
        $response = $client->get(
            $this->serverVerifyUri,
            [
                'headers' => ['Authorization' => 'Bearer ' . $access_token],
                'timeout' => 2,
                'connect_timeout' => 2,
            ]
        );

        try {
            $userData = json_decode($response->getBody()->getContents(), true);
        }
        catch(\Exception $e)
        {
            throw new UsernameNotFoundException(sprintf('User for Access Token "%s" does not exist or is invalid.', $access_token));
        }

        if ($userData) {
            return new OAuth2User($access_token, $userData['client_id'], $userData['user_id'], explode(' ', $userData['scope']));
        }

        throw new UsernameNotFoundException(sprintf('User for Access Token "%s" does not exist or is invalid.', $access_token));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof OAuth2User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByAccessToken($user->getAccessToken());
    }

    public function supportsClass($class)
    {
        return $class === 'OAuth2\ClientBundle\Security\User\OAuth2User';
    }
}
