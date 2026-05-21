<?php

namespace App\Domain\Exception;

class StockInsuffisantException extends \Exception
{
    public function __construct(string $message = "Stock insuffisant")
    {
        parent::__construct($message);
    }
}