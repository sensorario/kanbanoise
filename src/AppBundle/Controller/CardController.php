<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Card;
use AppBundle\Entity\Status;
use AppBundle\Responses\KResponse as ResponseBuilder;
use Doctrine\ORM\EntityManager;
use Kanban\Actors\BoardLimitChecker;
use Kanban\Actors\CardCounter;
use Kanban\Actors\CardMover;
use Kanban\Actors\DestinationFinder;
use Kanban\Actors\LimitChecker;
use Kanban\Actors\TagFinder;
use Kanban\Actors\WipChecker;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("card")
 */
class CardController extends Controller
{
    private $installer;

    public function __construct(\AppBundle\Component\Installer $installer)
    {
        $this->installer = $installer;
    }

    /**
     * @Route("/kanban", name="kanban")
     * @Method("GET")
     */
    public function kanbanAction(
        EntityManager $manager,
        WipChecker $wipChecker,
        CardCounter $cardCounter
    ) {
        $this->installer->verify();

        $statuses = $manager
            ->getRepository('AppBundle:Status')
            ->findAll();

        $tasks = [];
        $bugs = [];
        foreach ($statuses as $status) {
            $tasks[$status->getName()] = $manager
                ->getRepository('AppBundle:Card')
                ->findBy([
                    'status' => $status->getId(),
                    'type'   => 'task',
                ], [
                    'position' => 'ASC',
                ]);

            $bugs[$status->getName()] = $manager
                ->getRepository('AppBundle:Card')
                ->findBy([
                    'status' => $status->getId(),
                    'type'   => 'bug',
                ], [
                    'position' => 'ASC',
                ]);
        }

        return $this->render('card/kanban.html.twig', array(
            'cards'           => $tasks,
            'bugs'            => $bugs,
            'statuses'        => $statuses,
            'board_wip'       => $wipChecker->getBoardWipLimit(),
            'number_of_cards' => $cardCounter->numberOfCountableCards(),
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
        EntityManager $entityManager,
        LimitChecker $columnLimitChecker,
        BoardLimitChecker $boardChecker
    ) {
        if ($boardChecker->isBoardLimitReached()) {
            $this->addFlash('notice', 'wip board limit reached');
            return $this->redirectToRoute('kanban');
        }

        $card = new Card();
        $form = $this->createForm('AppBundle\Form\CardType', $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $columnLimitChecker->setCard($card);
            if ($columnLimitChecker->isColumnLimitReached($newCard = true)) {
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

        $projects = $entityManager
            ->getRepository('AppBundle:Project')
            ->findAll();

        $members = $entityManager
            ->getRepository('AppBundle:Member')
            ->findAll();

        return $this->render('card/new.html.twig', array(
            'statuses' => $statuses,
            'members'  => $members,
            'projects' => $projects,
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
        EntityManager $entityManager,
        TagFinder $finder
    ) {
        $deleteForm = $this->createDeleteForm($card);
        $editForm = $this->createForm('AppBundle\Form\CardType', $card);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->persist($card);
            $entityManager->flush();


            foreach ($card->getTags() as $tag) {
                $tag->removeCard($card);
                $card->removeTag($tag);
                $entityManager->persist($tag);
                $entityManager->persist($card);
                $entityManager->flush();
            }


            $tags = str_getcsv($request->request->get('tags'));
            foreach ($tags as $tagName) {
                if ('' != trim($tagName)) {
                    if (!$finder->knownTag($tagName)) {
                        $finder->saveNewTag($tagName);
                    }
                    $tag = $finder->catchTagWithName($tagName);

                    $card->addTag($tag);
                    $entityManager->persist($card);
                    $entityManager->flush();
                }
            }


            return $this->redirectToRoute('kanban');
        }

        $statuses = $entityManager
            ->getRepository('AppBundle:Status')
            ->findAll();

        $types = $entityManager
            ->getRepository('AppBundle:CardType')
            ->findAll();

        $projects = $entityManager
            ->getRepository('AppBundle:Project')
            ->findAll();

        $members = $entityManager
            ->getRepository('AppBundle:Member')
            ->findAll();

        $tags = [];
        foreach ($card->getTags()->toArray() as $tag) {
            $tags[] = $tag->getName();
        }

        return $this->render('card/edit.html.twig', array(
            'members'     => $members,
            'statuses'    => $statuses,
            'types'       => $types,
            'projects'    => $projects,
            'card'        => $card,
            'tags'        => implode(',', $tags),
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
