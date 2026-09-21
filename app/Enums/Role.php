<?php

namespace App\Enums;

enum Role: string
{
    case OWNER = 'owner';
    case WRITER = 'writer';
    case READER = 'reader';
}
