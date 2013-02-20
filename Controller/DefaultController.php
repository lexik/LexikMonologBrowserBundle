<?php

namespace Lexik\Bundle\LexikMonologDoctrineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LexikMonologDoctrineBundle:Default:index.html.twig', array('name' => $name));
    }
}
