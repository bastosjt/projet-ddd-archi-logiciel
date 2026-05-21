<?php

namespace App\Application\Command\AnnulerCommande;
use App\Domain\Repository\CommandeRepositoryInterface;
use App\Domain\Model\StatutCommande;
use App\Domain\Exception\AnnulationImpossibleException;

class AnnulerCommandeHandler
{
    public function __construct(
        private CommandeRepositoryInterface $commandeRepository,
    ) {}

    public function __invoke(AnnulerCommandeCommand $command): void
    {
        $commande = $this->commandeRepository->findById($command->commandeId);

        if ($commande === null) {
            throw new \RuntimeException("Commande #{$command->commandeId} introuvable.");
        }

        if ($commande->getStatut() !== StatutCommande::EN_ATTENTE) {
            throw new AnnulationImpossibleException();
        }

        $commande->setStatut(StatutCommande::ANNULEE);
        $this->commandeRepository->save($commande);
    }
}