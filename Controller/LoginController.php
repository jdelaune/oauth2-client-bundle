<?php

namespace OAuth2\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="_login")
     * @Method({"GET"})
     */
    public function loginAction()
    {
        // Do Nothing
    }
}
