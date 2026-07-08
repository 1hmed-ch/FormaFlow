<?php

namespace App\Enums;

enum FormationStatus: string
{
    case PLANIFIEE = "Planifiee";
    case EN_COURS = "En cours";
    case TERMINEE = "Terminee";
    case ANNULEE = "Annulee";
}
