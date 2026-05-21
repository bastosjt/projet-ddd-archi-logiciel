<?php

namespace App\Application\Query;

class MesCommandesQuery
{
    public function __construct(
        public string $email,
    ) {}
}