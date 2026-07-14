<?php

namespace App\Exceptions;

use Exception;

/**
 * Levée lorsqu'un document (attestation, fiche, récapitulatif...) ne peut
 * pas être généré car les règles de gestion du document ne sont pas
 * remplies (ex. données manquantes, aucune formation éligible, etc.).
 */
class DocumentGenerationException extends Exception
{
    //
}
