<?php

namespace App\Domain\Model;

class Produit
{
    private ?int $id = null;
    private string $nom;

    private float $prix;

    private int $stock;

    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function getPrix(): float { return $this->prix; }
    public function setPrix(float $prix): void { $this->prix = $prix; }
    public function getStock(): int { return $this->stock; }
    public function setStock(int $stock): void { $this->stock = $stock; }
}
