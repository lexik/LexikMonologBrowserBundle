<?php

namespace Lexik\Bundle\LexikMonologDoctrineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $query = $this->getLogRepository()->getLogsQueryBuilder();

        $pagination = $this->get('knp_paginator')->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10
        );

        return $this->render('LexikMonologDoctrineBundle:Default:index.html.twig', array(
            'pagination'  => $pagination,
            'base_layout' => $this->getBaseLayout(),
        ));
    }

    public function showAction($id)
    {
        $log = $this->getLogRepository()->getLogById($id);

        if (null === $log) {
            throw $this->createNotFoundException('The log entry does not exist');
        }

        return $this->render('LexikMonologDoctrineBundle:Default:show.html.twig', array(
            'log'         => $log,
            'base_layout' => $this->getBaseLayout(),
        ));
    }

    /**
     * @return string
     */
    public function getBaseLayout()
    {
        return $this->container->getParameter('lexik_monolog_doctrine.base_layout');
    }

    /**
     * @return \Lexik\Bundle\LexikMonologDoctrineBundle\Model\LogRepository
     */
    protected function getLogRepository()
    {
        return $this->get('lexik_monolog_doctrine.model.log_repository');
    }
}
