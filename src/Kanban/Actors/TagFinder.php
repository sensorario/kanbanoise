<?php

namespace Kanban\Actors;

use AppBundle\Entity\Tag;

class TagFinder
{
    private $manager;

    public function __construct(\Doctrine\ORM\EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function knownTag(string $tagName) : bool
    {
        try {
            $this->catchTagWithName($tagName);
        } catch(\RuntimeException $exception) {
            return false;
        }

        return true;
    }

    public function catchTagWithName(string $tagName)
    {
        $tag = $this->manager->getRepository('AppBundle:Tag')
            ->findOneBy([
                'name' => $tagName,
            ]);

        if (!$tag) {
            throw new \RuntimeException(
                'Oops! Tag "' . $tagName . '" not found'
            );
        }

        return $tag;
    }

    public function saveNewTag(string $tagName)
    {
        $tag = new Tag();
        $tag->setName($tagName);
        $this->manager->persist($tag);
        $this->manager->flush();
    }
}
