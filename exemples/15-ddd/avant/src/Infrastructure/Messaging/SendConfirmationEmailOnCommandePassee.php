<?php

namespace App\Infrastructure\Messaging;

use App\Domain\Event\CommandePassee;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class SendConfirmationEmailOnCommandePassee
{
    public function __construct(
        private MailerInterface $mailer,
    ) {}

    public function __invoke(CommandePassee $event): void
    {
        $email = (new Email())
            ->from('boutique@exemple.fr')
            ->to($event->clientEmail)
            ->subject("Confirmation de votre commande #{$event->commandeId}")
            ->html($this->renderHtml($event));

        $this->mailer->send($email);
    }

    private function renderHtml(CommandePassee $event): string
    {
        $total = number_format($event->total, 2, ',', ' ');

        return <<<HTML
            <h1>Confirmation de commande</h1>
            <p>Bonjour,</p>
            <p>Votre commande #{$event->commandeId} a bien ete enregistree.</p>
            <p>Total TTC : {$total} &euro;</p>
            <p>Merci pour votre confiance.</p>
            HTML;
    }
}
