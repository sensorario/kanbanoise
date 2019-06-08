<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("stats")
 */
class UsersStatsController extends Controller
{
    /**
     * @Route("/users/stats/{username}", name="users_stats")
     * @Method("GET")
     */
    public function statsAction(
        EntityManagerInterface $manager,
        string $username
    ) {
        $userCards = $manager->getRepository('AppBundle:Card')->findBy([
            'member' => $username,
        ]);

        return $this->render('stats/user.html.twig', [
            'userCards' => $userCards,
        ]);
    }
}
