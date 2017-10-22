<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Card;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\UseCase\CardRegression;

/**
 * @Route("card")
 */
class CardController extends Controller
{
    /**
     * @Route("/kanban/card/{card}/regress", name="card_regress")
     * @Method("GET")
     */
    public function regressAction(
        Card $card,
        EntityManager $manager,
        CardRegression $cardRegression
    ) {
        $cardRegression->setCard($card);
        $cardRegression->execute();

        return $this->redirectToRoute('kanban');
    }

    /**
     * @Route("/kanban/card/{card}/progress", name="card_progress")
     * @Method("GET")
     */
    public function progressAction(Card $card, EntityManager $manager)
    {
        /** @todo implement use case 'CardProgression' */
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
        $bugs = [];
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
     * @Route("/cards", name="cards")
     * @Method("GET")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cards = $em->getRepository('AppBundle:Card')->findAll();

        return $this->render('card/cards.html.twig', array(
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
            $cardInBoard = count($entityManager->getRepository('AppBundle:Card')->findAll());
            $boards = $entityManager->getRepository('AppBundle:Board')->findAll();

            if (count($boards) == 0) {
                throw new \RuntimeException(
                    'Oops! No boards found'
                );
            }

            $boardConf = $boards[0];
            if ($cardInBoard >= $boardConf->getWipLimit()) {
                $this->get('logger')->critical('threshold reached');
                $this->addFlash('notice', 'wip board limit reached');
                return $this->redirectToRoute('kanban');
            }

            $cardInStatus = $entityManager->getRepository('AppBundle:Card')->findby(['status' => $card->getStatus()]);
            $status = $entityManager->getRepository('AppBundle:Status')->findOneBy(['name' => $card->getStatus()]);
            $numberOfCardInCurrentStatus = count($cardInStatus);
            $limitOfCard = $status->getWipLimit();
            $postStatus = $request->request->get('appbundle_card')['status'];
            if ($limitOfCard > 0 && $limitOfCard <= $numberOfCardInCurrentStatus) {
                $this->get('logger')->critical('threshold reached');
                $this->addFlash('notice', 'wip column limit reached');
                return $this->redirectToRoute('kanban');
            }

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

        $members = $entityManager
            ->getRepository('AppBundle:Member')
            ->findAll();

        return $this->render('card/new.html.twig', array(
            'statuses' => $statuses,
            'members'  => $members,
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

            $cardInStatus = $entityManager->getRepository('AppBundle:Card')->findby(['status' => $card->getStatus()]);
            $status = $entityManager->getRepository('AppBundle:Status')->findOneBy(['name' => $card->getStatus()]);
            $numberOfCardInCurrentStatus = count($cardInStatus);
            $limitOfCard = $status->getWipLimit();
            $postStatus = $request->request->get('appbundle_card')['status'];

            $this->getDoctrine()->getManager()->flush();

            if ($numberOfCardInCurrentStatus > 0 && $limitOfCard <= $numberOfCardInCurrentStatus) {
                return $this->redirectToRoute('kanban');
            }

            return $this->redirectToRoute('kanban');
        }

        $statuses = $entityManager
            ->getRepository('AppBundle:Status')
            ->findAll();

        $types = $entityManager
            ->getRepository('AppBundle:CardType')
            ->findAll();

        $members = $entityManager
            ->getRepository('AppBundle:Member')
            ->findAll();

        return $this->render('card/edit.html.twig', array(
            'members'     => $members,
            'statuses'    => $statuses,
            'types'       => $types,
            'card'        => $card,
            'edit_form'   => $editForm->createView(),
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
