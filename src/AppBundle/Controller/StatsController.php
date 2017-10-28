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
        $numberOfBoards = count($manager->getRepository('AppBundle:Board')->findAll());
        $numberOfMembers = count($manager->getRepository('AppBundle:Member')->findAll());
        $numberOfProject = count($manager->getRepository('AppBundle:Project')->findAll());
        $numberOfTasks = count($manager->getRepository('AppBundle:Card')->findAll());

        return $this->render('stats/stats.html.twig', [
            'numberOfBoards' => $numberOfBoards,
            'numberOfMembers' => $numberOfMembers,
            'numberOfProject' => $numberOfProject,
            'numberOfTasks' => $numberOfTasks,
        ]);
    }
}
