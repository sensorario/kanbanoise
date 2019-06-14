<?php

namespace Kanbanoise\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    private $manager;

    private $dictionary;

    public function __construct(
        \Doctrine\ORM\EntityManager $manager,
        \Kanbanoise\ApiBundle\Components\Dictionary $dictionary
    ) {
        $this->manager = $manager;
        $this->dictionary = $dictionary;
    }

    /**
     * @Route("/api")
     */
    public function indexAction()
    {
        return new JsonResponse([
            "success" => true,
        ]);
    }

    /**
     * @Route("/api/resource/{name}")
     */
    public function collectionAction(string $name)
    {
        $this->dictionary->setResourceName($name);

        if (!$this->dictionary->knowResource()) {
            return new JsonResponse([
                "success" => "false",
                "message" => "undefined resource `$name`",
            ]);
        }

        $entityClassName = $this->dictionary->getEntityClassName();

        $entities = $this->manager->getRepository($entityClassName)->findAll();

        $collection = [];

        foreach ($entities as $entity) {
            if (!($entity instanceof \JsonSerializable)) {
                throw new \RuntimeException(
                    'Oops! Unserializable resource Exception'
                );
            }

            $collection[] = $entity->jsonSerialize();
        }

        return new JsonResponse([
            "data" => $collection,
        ]);
    }

    /**
     * @Route("/api/resource/{name}/{id}")
     */
    public function resourceAction(
        string $name,
        int $id
    ) {
        $this->dictionary->setResourceName($name);

        $entityClassName = $this->dictionary->getEntityClassName();

        $entity = $this->manager->getRepository($entityClassName)->findBy([
            'id' => $id,
        ]);

        if (!$entity) {
            return new JsonResponse([
                "success" => "false",
                "code" => 24564312,
            ]);
        }

        return new JsonResponse($entity);
    }

    /**
     * @Route("/api/resource/{name}/{id}/{relation}")
     */
    public function relationAction(
        string $name,
        int $id,
        string $relation
    ) {
        $this->dictionary->setResourceName($name);

        $queryBuilder = $this->manager->createQueryBuilder();
        $queryBuilder->select(array('m', 's')); // master, slave
        $queryBuilder->from($this->dictionary->getEntityClassName(), 'm');
        $queryBuilder->join('m.' . $relation, 's');
        $queryBuilder->where('m.id = :id');
        $queryBuilder->setParameter('id', $id);

        $results = $queryBuilder->getQuery()->getResult();

        if (count($results) == 0) {
            return new JsonResponse([
                "success" => "false",
                "code" => 87698760,
            ]);
        }

        $collection = [];
        foreach ($results as $record) {
            $tags = $record->{'get'.ucfirst($relation)}();
            foreach($tags as $tag) {
                $collection[] = $tag;
            }
        }

        if (!isset($record)) {
            return new JsonResponse([
                "success" => "false",
                "code" => 876978,
            ]);
        }

        return new JsonResponse(array_merge(
            $record->jsonSerialize(), [
            $relation => $collection,
        ]));
    }
}
