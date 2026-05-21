<?php

namespace App\Application\Command\PasserCommande;

use App\Domain\Repository\CommandeRepositoryInterface;
use App\Domain\Repository\ProduitRepositoryInterface;

class PasserCommandeHandler
{
    public function __construct(
        private CommandeRepositoryInterface $commandeRepository,
        private ProduitRepositoryInterface  $produitRepository,
    ) {}
}