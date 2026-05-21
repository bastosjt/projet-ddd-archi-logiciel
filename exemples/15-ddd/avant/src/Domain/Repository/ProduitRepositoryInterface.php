<?php

namespace App\Domain\Repository;
use App\Domain\Model\Produit;

interface ProduitRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): ?Produit;
    public function save(Produit $produit): void;
}