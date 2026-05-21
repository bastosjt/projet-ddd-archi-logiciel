<?php

namespace App\Domain\Exception;

class AnnulationImpossibleException extends \Exception
{
    public function __construct(string $message = "Annulation impossible")
    {
        parent::__construct($message);
    }
}