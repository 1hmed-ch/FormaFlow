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
    <div style="text-align: center; margin-top: 10px; margin-bottom: 30px;">
        <h2 style="text-decoration: underline; letter-spacing: 1px; font-size: 14pt; margin: 0; font-family: 'DejaVu Serif', 'Times New Roman', serif; font-style: italic;">
            DÉCLARATION SUR L'HONNEUR
        </h2>
    </div>

    <div style="font-family: 'DejaVu Serif', 'Times New Roman', serif; font-size: 11pt; line-height: 1.8; text-align: left; margin: 0 35px;">
        <p style="margin-bottom: 16px;"><i>Nous soussignés,</i></p>

        <div style="margin-left: 25px; margin-bottom: 26px;">
            <p style="margin: 8px 0;">&bull; <strong>Raison sociale :</strong> {{ $entreprise->raison_sociale }}</p>
            <p style="margin: 8px 0;">&bull; <strong>Représentée par :</strong> {{ $gerant->prenom }} {{ $gerant->nom }}</p>
            <p style="margin: 8px 0;">&bull; <strong>Fonction :</strong> {{ $gerant->fonction }}</p>
        </div>

        <p style="margin-bottom: 10px;"><i>Déclarons sur l'honneur :</i></p>

        <ul style="list-style-type: none; padding-left: 20px; margin: 10px 0 30px 0;">
            <li style="margin-bottom: 18px;">
                - n'avoir fait recours à aucun autre organisme ou projet pour le financement de cette étude
                (<u><strong>{{ $typeFormation->getLabel() }}</strong></u>) pour l'année <u><strong>{{ $annee }}</strong></u> ;
            </li>
            <li style="margin-bottom: 18px;">
                - n'être membre d'aucun autre GIAC ;
            </li>
            <li style="margin-bottom: 18px;">
                - n'avoir aucun lien organique ou familial avec l'organisme d'intervention qui va mener <u>cette étude</u>.
            </li>
        </ul>

        <div style="margin-top: 90px; text-align: right;">
            <p style="margin: 0;">Fait à <strong>{{ $ville }}</strong>, le <strong>{{ $dateEdition->format('d/m/Y') }}</strong></p>
            <br><br>
            <p style="margin-right: 30px; margin-top: 0;"><strong><u>Cachet et Signature légalisée :</u></strong></p>
        </div>
    </div>
@endsection