<?php

namespace OAuth2\ClientBundle\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OAuth2AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    protected $client;

    function __construct(array $client)
    {
        $this->client = $client;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        // Are we using the stored referer?
        var_dump('here');
        exit;
        if ($this->client['redirect_uri'] === 'referer' && $request->headers->has('referer')) {
            $uri = $request->headers->get('referer');
        }
        else {
            $uri = $this->client['redirect_url'];
        }

        return new RedirectResponse($uri);
    }
}