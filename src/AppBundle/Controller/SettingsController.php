<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("settings")
 */
class SettingsController extends Controller
{
    /**
     * @Route("/settings", name="settings")
     * @Method({"GET", "POST"})
     */
    public function regressAction(
        EntityManager $manager,
        Request $request
    ) {
        $board = $manager->getRepository('AppBundle:Board')->findOneBy([
            'owner' => 'sensorario',
        ]);

        $editForm = $this->createForm('AppBundle\Form\BoardType', $board);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $manager->persist($board);
            $manager->flush();
        }

        return $this->render('settings/settings.html.twig', [
            'edit_form' => $editForm->createView(),
        ]);
    }
}
