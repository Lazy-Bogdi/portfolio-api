<?php

namespace App\Enum;

enum SkillCategory: string
{
    case Backend = 'backend';
    case Frontend = 'frontend';
    case Devops = 'devops';
    case Other = 'other';
}
