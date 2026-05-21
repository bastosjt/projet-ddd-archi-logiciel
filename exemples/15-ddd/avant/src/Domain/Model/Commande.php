<?php

namespace App\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Commande
{
    private StatutCommande $statut = StatutCommande::EN_ATTENTE;

    private ?int $id = null;
    private string $clientEmail;

    private float $total = 0;

    private \DateTime $createdAt;

    private Collection $lignes;

    public function __construct()
    {
        $this->lignes    = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getClientEmail(): string { return $this->clientEmail; }
    public function setClientEmail(string $email): void { $this->clientEmail = $email; }
    public function getStatut(): StatutCommande { return $this->statut; }
    public function setStatut(StatutCommande $statut): void { $this->statut = $statut; }
    public function getTotal(): float { return $this->total; }
    public function setTotal(float $total): void { $this->total = $total; }
    public function getCreatedAt(): \DateTime { return $this->createdAt; }
    public function getLignes(): Collection { return $this->lignes; }
    public function addLigne(LigneCommande $ligne): void { $this->lignes->add($ligne); }
}
