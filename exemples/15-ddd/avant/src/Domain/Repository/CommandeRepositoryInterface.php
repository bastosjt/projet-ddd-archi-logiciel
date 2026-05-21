<?php

namespace App\Domain\Repository;
use App\Domain\Model\Commande;

interface CommandeRepositoryInterface
{
    public function findById(int $id): ?Commande;
    public function save(Commande $commande): void;
    public function findByEmail(string $email): array;
}