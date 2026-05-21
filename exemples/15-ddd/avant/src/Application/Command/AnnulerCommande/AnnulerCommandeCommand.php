<?php

namespace App\Application\Command\AnnulerCommande;

class AnnulerCommandeCommand
{
    public function __construct(
        public int $commandeId,
    ) {}
}