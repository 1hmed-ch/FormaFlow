{{--
    G3 - Fiche de Renseignement de l'Organisme de Conseil (dossier GIAC).

    Contenu entièrement dérivé de EntrepriseFormation::current() : ce
    document décrit notre propre organisme, pas l'entreprise cliente. Il est
    néanmoins généré et archivé par entreprise cliente puisqu'il fait partie
    intégrante de chaque dossier GIAC déposé.

    Variables attendues : $organisme (EntrepriseFormation), $giacLogo,
    $dateEdition.
--}}
@extends('documents.giac.giacBase')

@section('documentTitle', "C) Fiche de Renseignement de l'Organisme de Conseil")

@section('content')
    <div class="field-line">
        <span class="field-label">Raison Sociale :</span>
        <span class="dotted-fill wide">{{ $organisme->raison_sociale }}</span>
    </div>

    <div class="field-line">
        <span class="field-label">Statut Juridique :</span>
        <span class="dotted-fill">{{ $organisme->statut_juridique }}</span>
        <span class="field-label">Date de Création :</span>
        <span class="dotted-fill">{{ optional($organisme->date_creation)->format('d/m/Y') }}</span>
    </div>

    <div class="field-line">
        <span class="field-label">Adresse :</span>
        <span class="dotted-fill wide">{{ $organisme->siege_social }}</span>
    </div>

    <div class="field-line">
        <span class="field-label">Tel. :</span>
        <span class="dotted-fill">{{ $organisme->telephone }}</span>
        <span class="field-label">Fax :</span>
        <span class="dotted-fill">{{ $organisme->fax }}</span>
        <span class="field-label">Mail :</span>
        <span class="dotted-fill">{{ $organisme->email }}</span>
    </div>

    <div class="field-line">
        <span class="field-label">Nom du Responsable :</span>
        <span class="dotted-fill">{{ $organisme->representant_nom }}</span>
        <span class="field-label">Fonction :</span>
        <span class="dotted-fill">{{ $organisme->representant_fonction }}</span>
    </div>

    <div class="field-line">
        <span class="field-label">N° de C.N.S.S :</span>
        <span class="dotted-fill">{{ $organisme->cnss }}</span>
        <span class="field-label">N° du Registre de Commerce :</span>
        <span class="dotted-fill">{{ $organisme->rc }}</span>
    </div>

    <div class="field-line">
        <span class="field-label">Identifiant Fiscal (I.F) :</span>
        <span class="dotted-fill">{{ $organisme->if }}</span>
        <span class="field-label">Patente :</span>
        <span class="dotted-fill">{{ $organisme->patente }}</span>
    </div>

    <div class="g-box">
        <div class="g-box-title">Domaine(s) de Compétence</div>
        @forelse(($organisme->domaines_competence ?? []) as $domaine)
            <div class="field-line">- {{ $domaine }}</div>
        @empty
            <div class="field-line dotted-fill wide">&nbsp;</div>
        @endforelse
    </div>

    <div class="g-box">
        <div class="g-box-title">Moyens Pédagogiques</div>
        @forelse(($organisme->moyens_pedagogiques ?? []) as $moyen)
            <div class="field-line">- {{ $moyen }}</div>
        @empty
            <div class="field-line dotted-fill wide">&nbsp;</div>
        @endforelse
    </div>

    <div class="g-box">
        <div class="g-box-title">Moyens Humains de l'Organisme</div>
        <table class="effectif-table">
            <tr>
                <th>Fonction</th>
                <th>Effectif Total actuel</th>
                <th>Dont étrangers</th>
            </tr>
            <tr>
                <td>Consultants/Experts permanents</td>
                <td>{{ $organisme->nb_experts_permanents }}</td>
                <td>{{ $organisme->nb_experts_permanents_etrangers }}</td>
            </tr>
            <tr>
                <td>Consultants/Experts vacataires</td>
                <td>{{ $organisme->nb_experts_vacataires }}</td>
                <td>{{ $organisme->nb_experts_vacataires_etrangers }}</td>
            </tr>
            <tr>
                <td>Animateurs/Formateurs</td>
                <td>{{ $organisme->nb_animateurs_formateurs }}</td>
                <td>{{ $organisme->nb_animateurs_formateurs_etrangers }}</td>
            </tr>
            <tr>
                <td>Autres employés</td>
                <td>{{ $organisme->nb_autres_employes }}</td>
                <td>{{ $organisme->nb_autres_employes_etrangers }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>{{ $organisme->effectif_total }}</strong></td>
                <td><strong>{{ $organisme->effectif_total_etrangers }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="field-line">
        L'organisme appartient-il à un groupe étranger ?
        &nbsp;&nbsp;
        <span class="radio">{{ $organisme->appartient_groupe_etranger ? '●' : '' }}</span> Oui
        &nbsp;&nbsp;
        <span class="radio">{{ $organisme->appartient_groupe_etranger ? '' : '●' }}</span> Non
    </div>

    <div class="signature-zone">
        <div class="field-line">- Date : {{ $dateEdition->format('d/m/Y') }}</div>
        <div class="field-line">- Cachet et Signature :</div>
    </div>
@endsection
