@extends('documents.layouts.base')

@section('documentTitle','')

@push('styles')
<style>
    @page {
        margin: 40px 50px;
    }

    .document-title-container, 
    .document-header,
    header .document-title { 
        display: none !important; 
    }
    .document-title-block div {
        display: none !important;
    }
    
   body > .document-title-block {
        display: none !important;
        margin: 0 !important;
        padding: 0 !important;
        height: 0 !important;
    }
    header,
    footer {
        display: none !important;
    }
.custom-header-section {
        display: block !important;
        text-align: center;
        margin-top: 0;
        margin-bottom: 40px;
    }

    .custom-header-wrapper {
        display: inline-block;
        background-color: #000000; 
        padding: 0;
    }

    .custom-header-title {
        border: 3px double #000000;
        background: #ffffff;
        padding: 10px 24px;
        
        margin: -5px 5px 5px -5px; 
        text-transform: none; 
        display: block;
        font-size: 16px;
        font-weight: bold;
        color: #000000;
        font-family: 'Times New Roman',Times, serif;
    }

    .doc-header-line {
        font-size: 12px;
        margin-bottom: 4px;
    }

    table.eval-info {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin: 10px 0 14px 0;
        page-break-inside: avoid;
    }

    table.eval-info td {
        border: 1px solid #000;
        padding: 4px 8px;
        font-size: 11px;
        vertical-align: top;
        line-height: 1.6;
    }

    h3.eval-section-title {
        font-size: 13px;
        margin: 10px 0 4px 0;
    }

    table.eval-criteres {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        page-break-inside: avoid;
    }

    table.eval-criteres th,
    table.eval-criteres td {
        border: 1px solid #000;
        padding: 3px 6px;
        font-size: 9.5px;
        text-align: left;
    }

    table.eval-criteres th {
        background-color: #ffffff;
        text-align: center;
    }

    table.eval-criteres td.pct,
    table.eval-criteres th.pct {
        text-align: right;
        width: 45px;
        padding: 3px 4px;
    }

    table.aspects {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        page-break-inside: avoid;
    }

    table.aspects th,
    table.aspects td {
        border: 1px solid #000;
        padding: 4px 6px;
        font-size: 11px;
        width: 33.33%;
    }

    table.aspects th {
        text-align: center;
        font-weight: bold;
        background-color: #ffffff;
    }

    table.aspects td {
        height: 70px;
        vertical-align: top;
    }

    .eval-footer-row {
        margin-top: 20px;
        overflow: auto;
    }

    .eval-footer-row .signature {
        float: left;
        font-size: 11px;
        font-weight: bold;
    }

    .eval-footer-row .cachet {
        float: right;
        font-size: 11px;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
   <div class="custom-header-section">
        <div class="custom-header-wrapper">
            <div class="custom-header-title">
                Fiche d'évaluation synthétique par groupe
            </div>
        </div>
   </div>

    <div class="doc-header-line"><strong>Nom de l'entreprise :</strong> {{ $entreprise->raison_sociale }}</div>
    <div class="doc-header-line"><strong>Nom de l'organisme de formation :</strong> {{ $organisme->raison_sociale ?? config('app.name') }}</div>

    <table class="eval-info">
        <tr>
            <td style="width: 46%;">
                <strong>Thème :</strong> {{ $theme->intitule }}<br>
                <strong>Date :</strong> Du {{ \Carbon\Carbon::parse($theme->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($theme->date_fin)->format('d/m/Y') }}<br>
                <strong>Animateur :</strong> {{ $formateur->full_name }}
            </td>
            <td style="width: 27%;">
                <strong>Nombre de participants :</strong> {{ $nombreParticipants }}<br>
                <strong>Lieu :</strong> {{ $groupe->lieu ?? '—' }}
            </td>
            <td style="width: 27%;">
                <strong>N° du Groupe :</strong> {{ $groupe->libelle }}<br>
                <strong>Ville :</strong> {{ $ville }}
            </td>
        </tr>
    </table>

    <h3 class="eval-section-title">EVALUATION CRITÈRE (Synthèse)</h3>

    @php
        $criteres = [
            'Conditions de réalisation' => [
                "L'information concernant la formation a été complète",
                "La durée et le rythme de la formation étaient conformes",
                "Les documents annoncés ont été remis aux participants",
                "Les documents remis constituent une aide à l'assimilation des contenus",
                "Les contenus de la formation étaient adaptés à mon niveau initial",
                "Les conditions matérielles étaient satisfaisantes",
            ],
            'Compétences techniques et pédagogiques' => [
                "Le formateur dispose des compétences techniques nécessaires",
                "Le formateur dispose des compétences pédagogiques",
                "Le formateur a su créer ou entretenir une ambiance dans le groupe en formation",
                "Les moyens pédagogiques étaient adaptés au contenu de la formation",
            ],
            'Atteinte des objectifs' => [
                "Les objectifs de la formation correspondent aux objectifs professionnels",
                "Les objectifs recherchés ont été atteints",
                "La formation permet d'améliorer les compétences professionnelles",
            ],
        ];
    @endphp

    @foreach ($criteres as $section => $lignes)
        <table class="eval-criteres">
            <thead>
                <tr>
                    <th style="width: 55%; text-align: left;">{{ $section }}</th>
                    <th class="pct">Pas du tout</th>
                    <th class="pct">Peu</th>
                    <th class="pct">Moyen</th>
                    <th class="pct">Tout à fait</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lignes as $ligne)
                    <tr>
                        <td>{{ $ligne }}</td>
                        <td class="pct">%</td>
                        <td class="pct">%</td>
                        <td class="pct">%</td>
                        <td class="pct">100 %</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <table class="aspects">
        <thead>
            <tr>
                <th>Aspects à développer</th>
                <th>Aspects à clarifier</th>
                <th>Aspects à supprimer</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>

    <div class="eval-footer-row">
        <div class="signature">Emargement de l'animateur</div>
        <div class="cachet">Cachet de l'organisme de formation</div>
    </div>
@endsection