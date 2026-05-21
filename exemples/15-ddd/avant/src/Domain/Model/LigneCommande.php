<?php

namespace App\Domain\Model;

class LigneCommande
{
    private ?int $id = null;
    private Commande $commande;
    private Produit $produit;
    private int $quantite;

    private float $prixUnitaire;

    public function getId(): ?int { return $this->id; }
    public function getCommande(): Commande { return $this->commande; }
    public function setCommande(Commande $commande): void { $this->commande = $commande; }
    public function getProduit(): Produit { return $this->produit; }
    public function setProduit(Produit $produit): void { $this->produit = $produit; }
    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $quantite): void { $this->quantite = $quantite; }
    public function getPrixUnitaire(): float { return $this->prixUnitaire; }
    public function setPrixUnitaire(float $prix): void { $this->prixUnitaire = $prix; }
}
