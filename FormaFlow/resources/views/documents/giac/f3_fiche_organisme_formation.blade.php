{{--
    Formulaire F3 (OFPPT / Contrats Spéciaux de Formation) - Fiche
    d'identification de l'Organisme de Formation.

    Document officiel distinct de la fiche "G3" du dossier GIAC (qui est la
    Fiche de Renseignement de l'Organisme de Conseil). Les deux partagent
    beaucoup de champs communs mais ne sont pas le même formulaire : celui-ci
    est le Formulaire F3 disponible sur https://csf.ofppt.org.ma, à
    reproduire à l'identique (police, bandeau noir, cases).

    Variables attendues : $organisme (EntrepriseFormation), $dateEdition.
--}}
@extends('documents.giac.ofpptBase')

@section('formNumber', 'Formulaire F3')
@section('documentTitle', "Fiche d'identification de l'Organisme de Formation")

@section('content')

    @php
        $domaines = array_pad($organisme->domaines_competence ?? [], 3, '');
        $moyens = array_pad($organisme->moyens_pedagogiques ?? [], 3, '');
    @endphp

    <table class="f3-box">
        <tr><td colspan="2" class="f3-label">Raison sociale:</td></tr>
        <tr><td colspan="2" class="f3-value">{{ $organisme->raison_sociale }}</td></tr>

        <tr>
            <td class="f3-label" style="width: 50%;">Forme juridique*:</td>
            <td class="f3-label">Date de création:</td>
        </tr>
        <tr>
            <td class="f3-value">{{ $organisme->statut_juridique }}</td>
            <td class="f3-value">{{ optional($organisme->date_creation)->format('d/m/Y') }}</td>
        </tr>

        <tr><td colspan="2" class="f3-label">Nom et prénom du gérant:</td></tr>
        <tr><td colspan="2" class="f3-value">{{ $organisme->gerant_prenom }} {{ $organisme->gerant_nom }}</td></tr>

        <tr><td colspan="2" class="f3-label">Adresse:</td></tr>
        <tr><td colspan="2" class="f3-value">{{ $organisme->siege_social }}</td></tr>

        <tr>
            <td class="f3-inline">
                <span class="f3-inline-label">Téléphone :</span>
                <span class="f3-inline-value">{{ $organisme->telephone }}</span>
            </td>
            <td class="f3-inline">
                <span class="f3-inline-label">Fax :</span>
                <span class="f3-inline-value">{{ $organisme->fax }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="f3-inline">
                <span class="f3-inline-label">Email :</span>
                <span class="f3-inline-value">{{ $organisme->email }}</span>
            </td>
        </tr>
    </table>

    <table class="f3-box">
        <tr>
            <td class="f3-inline-right">
                <span class="f3-inline-label">Patente:</span>
                <span class="f3-inline-value">{{ $organisme->patente }}</span>
            </td>
            <td class="f3-inline-right">
                <span class="f3-inline-label">Identifiant fiscal:</span>
                <span class="f3-inline-value">{{ $organisme->if }}</span>
            </td>
        </tr>
        <tr>
            <td class="f3-inline-right">
                <span class="f3-inline-label">N° RC:</span>
                <span class="f3-inline-value">{{ $organisme->rc }}</span>
            </td>
            <td class="f3-inline-right">
                <span class="f3-inline-label">N° CNSS:</span>
                <span class="f3-inline-value">{{ $organisme->cnss }}</span>
            </td>
        </tr>
    </table>

    <table class="f3-box">
        <tr>
            <td class="f3-label" style="width: 50%;">Domaines de compétence:</td>
            <td class="f3-label">Moyens matériels pédagogiques:</td>
        </tr>
        @for ($i = 0; $i < 3; $i++)
            <tr>
                <td class="f3-value">{{ $domaines[$i] }}</td>
                <td class="f3-value">{{ $moyens[$i] }}</td>
            </tr>
        @endfor
    </table>

    <table class="f3-box">
        <tr><td class="f3-label">Moyens humains de l'Organisme :</td></tr>
        <tr>
            <td style="padding: 0;">
                <table class="effectif-table-f3">
                    <tr>
                        <th>Fonction</th>
                        <th>Effectif total (actuel)</th>
                        <th>dont étrangers</th>
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
            </td>
        </tr>
    </table>

    <table class="f3-box">
        <tr>
            <td>
                L'organisme appartient-il à un groupe étranger ?
                &nbsp;&nbsp;&nbsp;
                <span class="radio">{{ $organisme->appartient_groupe_etranger ? '●' : '' }}</span> Oui
                &nbsp;&nbsp;
                <span class="radio">{{ $organisme->appartient_groupe_etranger ? '' : '●' }}</span> Non
            </td>
        </tr>
    </table>

    <table class="f3-box">
        <tr>
            <td style="width: 50%;" class="f3-label">Fait à:</td>
            <td style="width: 50%;" class="f3-label">Le:</td>
        </tr>
        <tr>
            <td class="f3-value">{{ $organisme->siege_social }}</td>
            <td class="f3-value">{{ $dateEdition->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="f3-label">Nom et prénom:</td>
            <td rowspan="5" class="f3-label" style="vertical-align: top;">
                Signature et cachet de l'Organisme:
                <div style="height: 90px;"></div>
            </td>
        </tr>
        <tr>
            <td class="f3-value">{{ $organisme->gerant_prenom }} {{ $organisme->gerant_nom }}</td>
        </tr>
        <tr>
            <td class="f3-label">Qualité:</td>
        </tr>
        <tr>
            <td class="f3-value">{{ $organisme->representant_fonction }}</td>
        </tr>
        <tr>
            <td class="footnote">
                * Pour les personnes physiques, joindre une attestation d'inscription au rôle des Patentes.
            </td>
        </tr>
    </table>

    <div class="portal-footer">
        Ce formulaire est disponible sur le Portail des CSF à l'adresse: http://csf.ofppt.org.ma.<br>
        Il peut être rempli sur l'écran en tant que formulaire PDF avant d'être imprimé.
    </div>

@endsection
