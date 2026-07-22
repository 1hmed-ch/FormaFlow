@extends('documents.giac.giacBase')

@section('documentTitle', "E) Fiche Technique de l'Etude")
@section('documentSubtitle', "d'Ingénierie de Formation")

@section('content')
    <div class="field-line">
        <span class="field-label">ENTREPRISE BENEFICIAIRE :</span>
        <span class="dotted-fill wide">{{ $entreprise->raison_sociale }}</span>
    </div>

    <div class="g-box">
        <div class="field-line">
            <strong>1) Nature de l'Action :</strong>
            {{ $etude->nature_action }}
        </div>

        <div class="field-line">
            - Diagnostic des Besoins en Formation :
            {{ $etude->diagnostic_besoins ?: '—' }}
        </div>

        <div class="field-line">
            - Elaboration d'un Plan de Formation :
            <span class="checkbox">{{ $etude->plan_formation ? 'X' : '' }}</span>
            {{ $etude->plan_formation ? 'Oui' : 'Non' }}
            @if($etude->plan_formation && $etude->plan_formation_annee)
                — Pour l'Année : {{ $etude->plan_formation_annee }}
            @endif
        </div>

        <div class="field-line">
            - Bilan de Compétence : {{ $etude->bilan_competence ?: '—' }}
        </div>

        <div class="field-line">
            - Autres (à préciser) : {{ $etude->autres_precisions ?: '—' }}
        </div>

        <div class="field-line">
            <strong>2) Résultats attendus de l'Action :</strong>
            {{ $etude->resultats_attendus }}
        </div>

        <div class="field-line">
            <strong>3) Période de Réalisation :</strong>
            du {{ $etude->periode_debut->format('d/m/Y') }}
            au {{ $etude->periode_fin->format('d/m/Y') }}
        </div>

        <div class="field-line">
            <strong>4) Nombre de jours d'Intervention :</strong>
            {{ $etude->nb_jours_intervention }}
        </div>

        <div class="field-line">
            <strong>5) Organisme d'Intervention :</strong>
            {{ $organisme->raison_sociale }}
        </div>
        <div class="field-line">* Adresse : {{ $organisme->siege_social }}</div>
        <div class="field-line">
            * N° de CNSS : {{ $organisme->cnss }}
            &nbsp;&nbsp; Mail : {{ $organisme->email }}
        </div>
        <div class="field-line">
            * Tel. : {{ $organisme->telephone }}
            &nbsp;&nbsp; Fax : {{ $organisme->fax }}
        </div>
        <div class="field-line">* R.C. : {{ $organisme->rc }}</div>
        <div class="field-line">
            * Personne(s) à contacter : {{ $organisme->representant_nom }}
        </div>
        <div class="field-line">
            * Fonction dans l'Entreprise : {{ $organisme->representant_fonction }}
        </div>

        <div class="field-line">
            <strong>6) Proposition d'Intervention du Prestataire</strong>
            (à joindre en annexe de cette fiche)
        </div>

        <div class="field-line">
            <strong>7) Coût de l'Action (Hors Taxe) :</strong>
            {{ number_format((float) $etude->cout_action, 2, ',', ' ') }} DH
        </div>
    </div>

    <div class="signature-zone">
        <div class="field-line">- Date : {{ $dateEdition->format('d/m/Y') }}</div>
        <div class="field-line">- Cachet et Signature :</div>
    </div>
@endsection
