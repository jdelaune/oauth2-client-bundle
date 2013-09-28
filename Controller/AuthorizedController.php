<?php

namespace OAuth2\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class AuthorizedController extends Controller
{
    /**
     * @Route("/authorized", name="oauth2_client_authorized")
     * @Method({"GET"})
     */
    public function authorizedAction()
    {
        // Do Nothing
    }
}
