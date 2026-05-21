<?php

namespace App\Domain\Model;

enum StatutCommande: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case EXPEDIEE = 'EXPEDIEE';
    case ANNULEE = 'ANNULEE';
}