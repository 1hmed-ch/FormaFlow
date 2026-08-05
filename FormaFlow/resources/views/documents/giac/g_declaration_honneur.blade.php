@extends('documents.layouts.base')

@push('styles')
<style>
    header {
        top: -90px;
        height: 90px;
    }
    .entete-image {
        max-height: 90px;
        max-width: 65%;
        height: auto;
        width: auto;
    }

    footer {
        bottom: 0;
        height: 55px;
    }
    .pied-page-image {
        max-height: 55px;
        max-width: 100%;
        height: auto;
        width: auto;
    }

    @page {
        margin: 105px 40px 75px 40px;
    }
     
</style>
@endpush

@section('content')
    <div style="font-family: 'Times New Roman', Times, serif; font-style: italic; font-size: 12pt; line-height: 1.8; padding: 0 35px;">

        <div style="text-align: center; margin-top: 10px; margin-bottom: 30px;">
            <h2 style="text-decoration: underline; letter-spacing: 1px; font-size: 14pt; margin: 0; font-family: 'DejaVu Serif', 'Times New Roman', serif; font-style: italic;">
                DÉCLARATION SUR L'HONNEUR
            </h2>
        </div>

        <p style="margin-bottom: 16px;"><i>Nous soussignés,</i></p>

        <div style="padding-left: 20px; margin-bottom: 26px;">
            <p style="margin: 8px 0;">&bull; <strong>Raison sociale :</strong> {{ $entreprise->raison_sociale ?? '..................................................' }}</p>
            <p style="margin: 8px 0;">
                &bull; <strong>Représentée par :</strong> 
                @if(!empty($gerant->prenom) || !empty($gerant->nom))
                    {{ trim(($gerant->prenom ?? '') . ' ' . ($gerant->nom ?? '')) }}
                @else
                    ..................................................
                @endif
            </p>
            <p style="margin: 8px 0;">&bull; <strong>Fonction :</strong> {{ $gerant->fonction ?? '..................................................' }}</p>
        </div>

        <p style="margin-bottom: 10px;"><i>Déclarons sur l'honneur :</i></p>

        <ul style="list-style-type: none; padding-left: 20px; margin: 10px 0 30px 0;">
            <li style="margin-bottom: 18px;">
                - n'avoir fait recours à aucun autre organisme ou projet pour le financement de cette étude
                <strong>{{ $typeFormation?->getLabel() ?? '........................' }}</strong> pour l'année <strong>{{ $annee ?? '........' }}</strong>;
            </li>
            <li style="margin-bottom: 18px;">
                - n'être membre d'aucun autre GIAC ;
            </li>
            <li style="margin-bottom: 18px;">
                - n'avoir aucun lien organique ou familial avec l'organisme d'intervention qui va mener cette étude.
            </li>
        </ul>
        <div style="margin-top: 50px; text-align: right;">
            <p style="margin: 0;">Fait à <strong>{{ $ville ?? '....................' }}</strong>, le <strong>{{ isset($dateEdition) ? $dateEdition->format('d/m/Y') : now()->format('d/m/Y') }}</strong></p>
            <p style="margin: 20px 0 0 0;"><strong>Cachet et Signature légalisée :</strong></p>
        </div>
    </div>
@endsection