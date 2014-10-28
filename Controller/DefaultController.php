<?php

namespace Lexik\Bundle\MonologBrowserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\DBAL\DBALException;

use Lexik\Bundle\MonologBrowserBundle\Form\LogSearchType;

/**
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 */
class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        try {
            $query = $this->getLogRepository()->getLogsQueryBuilder();

            $filter = $this->get('form.factory')->create(new LogSearchType(), null, array(
                'query_builder' => $query,
                'log_levels'    => $this->getLogRepository()->getLogsLevel(),
            ));

            $filter->submit($request->get($filter->getName()));

            $pagination = $this->get('knp_paginator')->paginate(
                $query,
                $request->query->get('page', 1),
                $this->container->getParameter('lexik_monolog_browser.logs_per_page')
            );
        } catch (DBALException $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            $pagination = array();
        }

        return $this->render('LexikMonologBrowserBundle:Default:index.html.twig', array(
            'filter'      => isset($filter) ? $filter->createView() : null,
            'pagination'  => $pagination,
            'base_layout' => $this->getBaseLayout(),
        ));
    }

    /**
     * @param Request $request
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction(Request $request, $id)
    {
        $log = $this->getLogRepository()->getLogById($id);

        if (null === $log) {
            throw $this->createNotFoundException('The log entry does not exist');
        }

        $similarLogsQuery = $this->getLogRepository()->getSimilarLogsQueryBuilder($log);

        $similarLogs = $this->get('knp_paginator')->paginate(
            $similarLogsQuery,
            $request->query->get('page', 1),
            10
        );

        return $this->render('LexikMonologBrowserBundle:Default:show.html.twig', array(
            'log'          => $log,
            'similar_logs' => $similarLogs,
            'base_layout'  => $this->getBaseLayout(),
        ));
    }

    /**
     * @return string
     */
    protected function getBaseLayout()
    {
        return $this->container->getParameter('lexik_monolog_browser.base_layout');
    }

    /**
     * @return \Lexik\Bundle\MonologBrowserBundle\Model\LogRepository
     */
    protected function getLogRepository()
    {
        return $this->get('lexik_monolog_browser.model.log_repository');
    }
}
