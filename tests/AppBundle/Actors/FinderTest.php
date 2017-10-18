<?php

namespace Tests\AppBundle\Actors;

use AppBundle\Actors\Finder;
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
}
