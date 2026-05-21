<?php

namespace App\Application\Command\PasserCommande;

class PasserCommande
{
    public function __construct(
        public string $email,
        public array  $items,
    ) {}
}
