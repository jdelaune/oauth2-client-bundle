<?php

namespace OAuth2\ClientBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Guzzle\Http\Client;

class OAuth2UserProvider implements UserProviderInterface
{
    private $verify_endpoint;

    public function __construct($verify_endpoint)
    {
        $this->verify_endpoint = $verify_endpoint;
    }

    public function loadUserByUsername($username)
    {
        return $this->loadUserByAccessToken($username);
    }

    public function loadUserByAccessToken($access_token)
    {
        // Verify Access Token and get details back
        $client = new Client();
        $request = $client->get(
            $this->verify_endpoint,
            array(
                'Authorization' => 'Bearer ' . $access_token
            ),
            array(
                'timeout' => 2,
                'connect_timeout' => 2,
            )
        );

        try {
            $response = $request->send();
            $userData = $response->json();
        }
        catch(\Exception $e)
        {
            throw new UsernameNotFoundException(sprintf('User for Access Token "%s" does not exist or is invalid.', $access_token));
        }

        if ($userData) {
            return new OAuth2User($access_token, $userData['client_id'], $userData['user_id'], new \DateTime('@' . $userData['expires']), explode(' ', $userData['scope']));
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
