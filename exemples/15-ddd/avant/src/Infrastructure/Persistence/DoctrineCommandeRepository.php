<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Model\Commande;
use App\Domain\Repository\CommandeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineCommandeRepository extends ServiceEntityRepository implements CommandeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function findById(int $id): ?Commande
    {
        return $this->find($id);
    }

    public function save(Commande $commande): void
    {
        $this->getEntityManager()->persist($commande);
        $this->getEntityManager()->flush();
    }

    public function findByEmail(string $email): array
    {
        return $this->findBy(['clientEmail' => $email], ['createdAt' => 'DESC']);
    }
}