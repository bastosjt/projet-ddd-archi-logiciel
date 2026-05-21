<?php

namespace App\Domain\Event;

class CommandeAnnulee
{
    public function __construct(
        public int $commandeId,
    ) {}
}