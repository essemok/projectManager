<?php

declare(strict_types=1);

namespace App\Model\User;


interface Flusher
{
    public function flush(): void;
}