<?php

namespace App\Application\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use App\Domain\Event\CommandePassee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface    $bus,
    ) {}

    // ----------------------------------------------------------------
    // Page d'accueil
    // ----------------------------------------------------------------
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    // ----------------------------------------------------------------
    // USE CASE 1 : Passer une commande
    // ----------------------------------------------------------------
    #[Route('/commandes/passer', name: 'commande_passer', methods: ['GET', 'POST'])]
    public function passerCommande(Request $request, PasserCommande $passerCommande): Response
    {
        //$commande = new Commande($request);
        //$passerCommande->passerLaCommande($commande);

        return $this->render('commande/passer.html.twig', ['produits' => $produits]);
    }

    // ----------------------------------------------------------------
    // USE CASE 2 : Annuler une commande
    // ----------------------------------------------------------------
    #[Route('/commandes/{id}/annuler', name: 'commande_annuler', methods: ['POST'])]
    public function annulerCommande(int $id): Response
    {
        $commande = $this->em->getRepository(Commande::class)->find($id);

        if (!$commande) {
            $this->addFlash('error', "Commande #$id introuvable.");
            return $this->redirectToRoute('commande_passer');
        }

        // Règle métier : on ne peut annuler qu'une commande EN_ATTENTE
        if ($commande->getStatut() !== Commande::STATUT_EN_ATTENTE) {
            $this->addFlash('error', "Impossible d'annuler une commande dont le statut est « {$commande->getStatut()} ».");
            return $this->redirectToRoute('commande_liste', ['email' => $commande->getClientEmail()]);
        }

        // Réapprovisionnement du stock
        foreach ($commande->getLignes() as $ligne) {
            $produit = $ligne->getProduit();
            $produit->setStock($produit->getStock() + $ligne->getQuantite());
        }

        $commande->setStatut(Commande::STATUT_ANNULEE);
        $this->em->flush();

        $this->addFlash('success', "Commande #{$commande->getId()} annulée, stocks restaurés.");
        return $this->redirectToRoute('commande_liste', ['email' => $commande->getClientEmail()]);
    }

    // ----------------------------------------------------------------
    // USE CASE 3 : Voir les commandes d'un client
    // ----------------------------------------------------------------
    #[Route('/commandes', name: 'commande_liste', methods: ['GET'])]
    public function mesCommandes(Request $request): Response
    {
        $email    = $request->query->get('email', '');
        $commandes = [];

        if ($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Email invalide.');
            } else {
                $commandes = $this->em->getRepository(Commande::class)->findBy(
                    ['clientEmail' => $email],
                    ['createdAt' => 'DESC']
                );
            }
        }

        return $this->render('commande/liste.html.twig', [
            'email'     => $email,
            'commandes' => $commandes,
        ]);
    }
}
