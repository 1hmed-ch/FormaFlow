@extends('documents.giac.giacBase')

@section('documentTitle', "D) Fiche Technique de l'Etude")
@section('documentSubtitle', 'du Diagnostic Stratégique')

@section('content')
    <div class="field-line">
        <span class="field-label">ENTREPRISE BENEFICIAIRE :</span>
        <span class="dotted-fill wide">{{ $entreprise->raison_sociale }}</span>
    </div>

    <div class="g-box">
        <div class="g-box-title">Nature du Projet de Développement de l'Entreprise</div>

        <div class="field-line">
            <span class="checkbox">{{ $etude->projet_marche_export ? 'X' : '' }}</span>
            Marché d'Exportation
            &nbsp;&nbsp;&nbsp;
            <span class="checkbox">{{ $etude->projet_investissement_techno ? 'X' : '' }}</span>
            Investissement Technologique
        </div>
        <div class="field-line">
            <span class="checkbox">{{ $etude->projet_mise_aux_normes ? 'X' : '' }}</span>
            Mise aux Normes
            &nbsp;&nbsp;&nbsp;
            <span class="checkbox">{{ $etude->projet_autre ? 'X' : '' }}</span>
            Autres à préciser :
            {{ $etude->projet_autre_precision ?: '—' }}
        </div>
    </div>

    <div class="g-box">
        <div class="g-box-title">Objectifs et Résultats Attendus du Diagnostic</div>
        <div class="field-line">{{ $etude->objectifs_resultats_attendus }}</div>
    </div>

    <div class="g-box">
        <div class="g-box-title">
            Proposition d'Intervention du Cabinet-Conseil
        </div>
        <div class="field-line" style="font-size: 10px;">
            (Joindre pour détails, l'offre soumise par ce Cabinet)
        </div>

        <div class="field-line">
            - Prestations Envisagées : {{ $etude->prestations_envisagees }}
        </div>
        <div class="field-line">
            - En vue de leur application durant l'année : {{ $etude->annee_application }}
        </div>
        <div class="field-line">
            - Durée Effective de l'Intervention (nombre de jours) :
            {{ $etude->duree_intervention_jours }}
        </div>
        <div class="field-line">
            - Date de démarrage : {{ $etude->date_demarrage->format('d/m/Y') }}
        </div>
        <div class="field-line">
            - Coût Prévisionnel de l'Intervention (en DH, H.T.) :
            {{ number_format((float) $etude->cout_previsionnel, 2, ',', ' ') }}
        </div>
    </div>

    <div class="g-box">
        <div class="g-box-title">Cabinet-Conseil chargé de l'Intervention</div>

        <div class="field-line">- Raison Sociale : {{ $organisme->raison_sociale }}</div>
        <div class="field-line">- Adresse : {{ $organisme->siege_social }}</div>
        <div class="field-line">
            - N° de CNSS : {{ $organisme->cnss }}
            &nbsp;&nbsp; Téléphone : {{ $organisme->telephone }}
            &nbsp;&nbsp; Fax : {{ $organisme->fax }}
        </div>
        <div class="field-line">
            - N° R.C. : {{ $organisme->rc }}
            &nbsp;&nbsp; Mail : {{ $organisme->email }}
        </div>
        <div class="field-line">
            - Responsable Principal à contacter : {{ $organisme->representant_nom }}
        </div>
    </div>

    <div class="signature-zone">
        <div class="field-line">- Date : {{ $dateEdition->format('d/m/Y') }}</div>
        <div class="field-line">- Cachet et Signature :</div>
    </div>
@endsection
