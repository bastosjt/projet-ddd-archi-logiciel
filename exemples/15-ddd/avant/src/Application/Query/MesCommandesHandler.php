<?php

namespace App\Application\Query;

use App\Domain\Repository\CommandeRepositoryInterface;

class MesCommandesHandler
{
    public function __construct(
        private CommandeRepositoryInterface $commandeRepository,
    ) {}

    public function __invoke(MesCommandesQuery $query): array
    {
        return $this->commandeRepository->findByEmail($query->email);
    }
}