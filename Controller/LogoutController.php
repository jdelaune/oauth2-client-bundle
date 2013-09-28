<?php

namespace OAuth2\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class LogoutController extends Controller
{
    /**
     * @Route("/logout", name="_logout")
     * @Method({"GET"})
     */
    public function logoutAction()
    {
        // Do Nothing
    }
}
