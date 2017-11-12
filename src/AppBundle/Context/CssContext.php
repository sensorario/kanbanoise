<?php

namespace AppBundle\Context;

use Behat\MinkExtension\Context\RawMinkContext;

class CssContext extends RawMinkContext
{
    /**
     * @Given class :class is not present
     */
    public function classIsNotPresent($classSelector)
    {
        $this->assertSession()->elementNotExists('css', '.' . $classSelector);
    }

    /**
     * @Given class :class is present
     */
    public function classIsPresent($classSelector)
    {
        $this->assertSession()->elementExists('css', '.' . $classSelector);
    }
}
