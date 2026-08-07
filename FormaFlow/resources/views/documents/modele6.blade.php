@php use App\Enums\gerantGender; @endphp
@extends('documents.layouts.base')

@section('documentTitle', "Attestation certifiant la réalisation des actions")
@section('documentSubtitle', 'Modèle 6')

@push('styles')
    <style>
        .document-title {
            text-transform: none;
        }

        .certification-text {
            text-align: justify;
            margin: 55px 35px 24px;
        }

        .themes-list {
            list-style: none;
            margin: 0 60px 40px;
            padding: 0;
        }

        .themes-list li {
            margin-bottom: 8px;
        }
    </style>
@endpush

@section('content')
    <p class="certification-text">
        Je {{$gerant->genre == gerantGender::Homme ? "soussigné M." : "soussignée Mme"}} {{ $gerant->prenom }}, en Qualité de
        {{$gerant->genre == gerantGender::Homme ? "Gérant" : "Gérante"}}, certifie par la présente que l'entreprise
        {{ $entreprise->raison_sociale }} a réalisé, au titre de l'exercice
        {{ $annee }}, les actions de formation citées ci-après dans le cadre des Contrats Spéciaux de Formation et a procédé à la liquidation des dépenses
        relatives des dites actions.
    </p>

    <ul class="themes-list">
        @foreach($themes as $theme)
            <li>- {{ $theme->intitule }}</li>
        @endforeach
    </ul>

    <div class="signature-block clearfix">
        <strong> {{$gerant->genre == gerantGender::Homme ? "M." : "Mme"}} {{ $gerant->prenom }} {{ $gerant->nom }}</strong><br>
        <strong>{{$gerant->genre == gerantGender::Homme ? "Gérant" : "Gérante"}}</strong>
    </div>
@endsection
