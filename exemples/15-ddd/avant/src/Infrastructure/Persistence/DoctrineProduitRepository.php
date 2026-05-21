<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Model\Produit;
use App\Domain\Repository\ProduitRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineProduitRepository extends ServiceEntityRepository implements ProduitRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findById(int $id): ?Produit
    {
        return $this->find($id);
    }

    public function save(Produit $produit): void
    {
        $this->getEntityManager()->persist($produit);
        $this->getEntityManager()->flush();
    }

    public function findAll(): array
    {
        return parent::findAll();
    }
}