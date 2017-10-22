<?php

use Kanban\Actors\Finder;
use PHPUnit\Framework\TestCase;

class FinderTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Oops! Entity class is not defined
     */
    public function testShouldBeInitializable()
    {
        $this->manager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->finder = new Finder(
            $this->manager
        );

        $this->finder->ensureEntityClasssIsDefined();
    }

    public function testLookForEntitiesByName()
    {
        $this->repository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with('entity:name')
            ->willReturn($this->repository);

        $name = 'foo';
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => $name]);

        $this->finder = new Finder(
            $this->manager
        );

        $this->finder->setEntity('entity:name');
        $this->finder->findByName($name);
    }

    public function testLookForEntitiesByPosition()
    {
        $this->repository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with('entity:name')
            ->willReturn($this->repository);

        $position = 42;
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['position' => $position]);

        $this->finder = new Finder(
            $this->manager
        );

        $this->finder->setEntity('entity:name');
        $this->finder->findByPosition($position);
    }

    public function testLookForEntitiesByAnyCriteria()
    {
        $this->repository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with('entity:name')
            ->willReturn($this->repository);

        $criteria = [
            'foo' => 'bar',
        ];

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($criteria);

        $this->finder = new Finder(
            $this->manager
        );

        $this->finder->setEntity('entity:name');
        $this->finder->findBy($criteria);
    }
}
