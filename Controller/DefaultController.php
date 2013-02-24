<?php

namespace Lexik\Bundle\LexikMonologDoctrineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $query = $this->get('lexik_monolog_doctrine.model.log_repository')->getLogsQueryBuilder();

        $pagination = $this->get('knp_paginator')->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10
        );

        return $this->render('LexikMonologDoctrineBundle:Default:index.html.twig', array(
            'pagination'  => $pagination,
            'base_layout' => $this->container->getParameter('lexik_monolog_doctrine.base_layout'),
        ));
    }
}
