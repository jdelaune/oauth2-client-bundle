<?php

namespace OAuth2\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('OAuth2ClientBundle:Default:index.html.twig', array('name' => $name));
    }
}
