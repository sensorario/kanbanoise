<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("stats")
 */
class StatsController extends Controller
{
    /**
     * @Route("/stats", name="stats")
     * @Method("GET")
     */
    public function regressAction(
        EntityManager $manager
    ) {
        $numberOfTasks = count($manager->getRepository('AppBundle:Card')->findAll());
        $numberOfBoards = count($manager->getRepository('AppBundle:Board')->findAll());

        return $this->render('stats/stats.html.twig', [
            'numberOfTasks' => $numberOfTasks,
            'numberOfBoards' => $numberOfBoards,
        ]);
    }
}
