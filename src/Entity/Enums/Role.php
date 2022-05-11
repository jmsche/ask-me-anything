<?php

declare(strict_types=1);

namespace App\Entity\Enums;

enum Role: string
{
    case Admin = 'ROLE_ADMIN';
    case SuperAdmin = 'ROLE_SUPER_ADMIN';
}
