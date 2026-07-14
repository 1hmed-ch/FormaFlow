<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Disque de stockage des documents générés
    |--------------------------------------------------------------------------
    |
    | Chaque document généré (attestation, fiche, récapitulatif...) est
    | conservé sur ce disque pour permettre son suivi/historique, en plus
    | d'être renvoyé immédiatement en téléchargement.
    |
    */
    'storage_disk' => env('DOCUMENTS_STORAGE_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Répertoire racine de stockage
    |--------------------------------------------------------------------------
    */
    'storage_path' => 'documents',

    /*
    |--------------------------------------------------------------------------
    | Réglages de mise en page par défaut (Dompdf)
    |--------------------------------------------------------------------------
    */
    'paper' => [
        'size' => 'a4',
        'orientation' => 'portrait',
    ],

    /*
    |--------------------------------------------------------------------------
    | Police par défaut
    |--------------------------------------------------------------------------
    |
    | DejaVu Sans est embarquée avec dompdf et couvre correctement les
    | caractères accentués français, contrairement aux polices "core" PDF.
    |
    */
    'default_font' => 'DejaVu Sans',

    /*
    |--------------------------------------------------------------------------
    | Options Dompdf
    |--------------------------------------------------------------------------
    */
    'dompdf_options' => [
        'isRemoteEnabled' => false,
        'isHtml5ParserEnabled' => true,
        'isPhpEnabled' => false,
        'chroot' => base_path(),
    ],

];
