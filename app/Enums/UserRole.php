<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'owner';
    case SMM = 'smm';
    case MEMBER = 'member';
}
