<?php

namespace App\Domain\Event;

class CommandePassee
{
    public function __construct(
        public int $commandeId,
        public string $clientEmail,
        public float $total,
    ) {}
}
