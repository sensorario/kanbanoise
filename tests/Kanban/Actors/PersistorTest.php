<?php

use Kanban\Actors\Persistor;
use PHPUnit\Framework\TestCase;

class PersistorTest extends TestCase
{
    public function testLookForEntitiesByPosition()
    {
        $this->manager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager->expects($this->once())
            ->method('persist')
            ->with('entity:name');

        $this->manager->expects($this->once())
            ->method('flush');

        $this->persistor = new Persistor(
            $this->manager
        );

        $this->persistor->save('entity:name');
    }
}
