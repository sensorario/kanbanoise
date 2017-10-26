<?php

namespace Kanban\Actors;

use AppBundle\Entity\Card;

class CardMover
{
    private $card;

    private $status;

    private $persistor;

    public function __construct(Persistor $persistor)
    {
        $this->persistor = $persistor;
    }

    public function setCard(Card $card)
    {
        $this->card = $card;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function move()
    {
        $this->card->setStatus($this->status);
        $this->persistor->save($this->card);
    }
}
