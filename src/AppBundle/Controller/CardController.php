<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Card;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("card")
 */
class CardController extends Controller
{
    /**
     * @Route("/kanban/card/{card}/regress", name="card_regress")
     * @Method("GET")
     */
    public function regressAction(Card $card, EntityManager $manager)
    {
        $statuses = $manager
            ->getRepository('AppBundle:Status')
            ->findAll();

        $previousWasPrevious = false;
        $previousState = '';
        foreach ($statuses as $status) {
            if ($previousWasPrevious == true) {
                $card->setStatus($previousState);
                $manager->persist($card);
                $manager->flush();
                return $this->redirectToRoute('kanban');
            }

            if ($status->getName() == $card->getStatus()) {
                $previousWasPrevious = true;
            } else {
                $previousState = $status->getName();
            }
        }

        return $this->redirectToRoute('kanban');
    }

    /**
     * @Route("/kanban/card/{card}/progress", name="card_progress")
     * @Method("GET")
     */
    public function progressAction(Card $card, EntityManager $manager)
    {
        $statuses = $manager
            ->getRepository('AppBundle:Status')
            ->findAll();

        $nextIsNext = false;
        foreach ($statuses as $status) {
            if ($nextIsNext == true) {
                $card->setStatus($status->getName());
                $manager->persist($card);
                $manager->flush();
                return $this->redirectToRoute('kanban');
            }

            if ($status->getName() == $card->getStatus()) {
                $nextIsNext = true;
            }
        }

        return $this->redirectToRoute('kanban');
    }

    /**
     * @Route("/kanban", name="kanban")
     * @Method("GET")
     */
    public function todoAction(EntityManager $manager)
    {
        $statuses = $manager
            ->getRepository('AppBundle:Status')
            ->findAll();

        $tasks = [];
        $bugs§ = [];
        foreach ($statuses as $status) {
            $tasks[$status->getName()] = $manager
                ->getRepository('AppBundle:Card')
                ->findBy([
                    'status' => $status->getName(),
                    'type'   => 'task',
                ]);

            $bugs[$status->getName()] = $manager
                ->getRepository('AppBundle:Card')
                ->findBy([
                    'status' => $status->getName(),
                    'type'   => 'bug',
                ]);
        }

        return $this->render('card/kanban.html.twig', array(
            'cards'    => $tasks,
            'bugs'     => $bugs,
            'statuses' => $statuses,
        ));
    }

    /**
     * Lists all card entities.
     *
     * @Route("/", name="card_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cards = $em->getRepository('AppBundle:Card')->findAll();

        return $this->render('card/index.html.twig', array(
            'cards' => $cards,
        ));
    }

    /**
     * @Route("/new", name="card_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(
        Request $request,
        EntityManager $entityManager
    ) {
        $card = new Card();
        $form = $this->createForm('AppBundle\Form\CardType', $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($card);
            $em->flush();

            return $this->redirectToRoute('card_show', array('id' => $card->getId()));
        }

        $statuses = $entityManager
            ->getRepository('AppBundle:Status')
            ->findAll();

        $types = $entityManager
            ->getRepository('AppBundle:CardType')
            ->findAll();

        return $this->render('card/new.html.twig', array(
            'statuses' => $statuses,
            'types'    => $types,
            'card'     => $card,
            'form'     => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="card_show")
     * @Method("GET")
     */
    public function showAction(Card $card)
    {
        $deleteForm = $this->createDeleteForm($card);

        return $this->render('card/show.html.twig', array(
            'card' => $card,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/edit", name="card_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(
        Request $request,
        Card $card,
        EntityManager $entityManager
    ) {
        $deleteForm = $this->createDeleteForm($card);
        $editForm = $this->createForm('AppBundle\Form\CardType', $card);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('card_edit', array('id' => $card->getId()));
        }

        $statuses = $entityManager
            ->getRepository('AppBundle:Status')
            ->findAll();

        $types = $entityManager
            ->getRepository('AppBundle:CardType')
            ->findAll();

        return $this->render('card/edit.html.twig', array(
            'statuses' => $statuses,
            'types' => $types,
            'card' => $card,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="card_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Card $card)
    {
        $form = $this->createDeleteForm($card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($card);
            $em->flush();
        }

        return $this->redirectToRoute('card_index');
    }

    private function createDeleteForm(Card $card)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('card_delete', array('id' => $card->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
