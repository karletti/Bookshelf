<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/queries", name="queries")
     */
    public function queryAction()
    {
        $rep = $this->getDoctrine()->getManager()->getRepository('AppBundle:Book');

        $doctrine = $rep->DoctrineQuery();
        $dql = $rep->DQLQuery();
        $sql = $rep->SQLQuery();

        return $this->render('default/queries.html.twig', array(
            'doctrine' => $doctrine,
            'dql' => $dql,
            'sql' => $sql
        ));
    }
}
