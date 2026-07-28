<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche d'identification de l'Organisme de Formation</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @page {
            margin: 40px 0 90px 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
        }

        .container {
            width: 85%; /* Controls the width. Decrease to make margins larger */
            max-width: 800px;
            margin: 0 auto; /* Centers the container horizontally */
        }

        .csf-header-label {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .csf-header-title {
            font-weight: bold;
            font-size: 26px;
            text-align: center;
            margin: 2px 0 8px 0;
        }

        .csf-header-bar {
            height: 6px;
            background-color: #000;
            margin-bottom: 12px;
        }

        .document-heading {
            font-weight: normal;
            font-size: 19px;
            text-align: center;
            font-family: Verdana, Geneva, sans-serif;
            margin-bottom: 14px;
        }

        .clearfix::after {
            content: "";
            display: block;
            clear: both;
        }

        .form-box {
            border: 1px solid #000;
            padding: 3px 2px 3px 2px;
            margin-bottom: 8px;
        }

        .field-label {
            font-size: 12px;
            margin-bottom: 2px;
        }

        .field-input {
            border: 1px solid #000;
            min-height: 15px;
            padding: 3px 6px;
            margin-bottom: 8px;
        }

        .field-input.small-input {
            min-height: 13px;
            padding: 2px 6px;
            margin-bottom: 4px;
        }

        .field-input:last-child {
            margin-bottom: 0;
        }

        .col-50 {
            width: 48%;
            margin-top: 5px;
        }

        .col-50:nth-child(1) {
            float: left;
        }

        .col-50:nth-child(2) {
            float: right;
        }

        .date-field {
            position: relative;
            padding-right: 20px;
        }

        .calendar-icon {
            position: absolute;
            top: 3px;
            right: 4px;
            width: 11px;
            height: 10px;
            border: 1px solid #000;
            background: #fff;
        }

        .calendar-icon::before {
            content: "";
            position: absolute;
            top: -3px;
            left: 2px;
            width: 1px;
            height: 3px;
            background: #000;
        }

        .calendar-icon::after {
            content: "";
            position: absolute;
            top: -3px;
            right: 2px;
            width: 1px;
            height: 3px;
            background: #000;
        }

        .inline-label-right {
            display: inline-block;
            width: 100px;
            text-align: right;
            margin-right: 6px;
        }

        .inline-label-left {
            display: inline-block;
            margin-right: 6px;
        }

        .inline-box {
            display: inline-block;
            border: 1px solid #000;
            min-width: 110px;
            min-height: 12px;
            padding: 2px 8px;
        }

        .inline-box-tel {
            display: inline-block;
            border: 1px solid #000;
            min-width: 160px;
            min-height: 12px;
            padding: 2px 8px;
            margin-right: 24px;
        }

        .inline-box-email {
            display: inline-block;
            border: 1px solid #000;
            width: 80%;
            min-height: 12px;
            padding: 2px 8px;
            margin-left: 24px;
        }

        .inline-row {
            margin-bottom: 6px;
        }

        .inline-row:last-child {
            margin-bottom: 0;
        }

        .grid-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 6px;
            margin-top: 4px;
        }

        .grid-table th, .grid-table td {
            padding: 4px 6px;
            border: none;
        }

        .col-fonction {
            width: 50%;
            text-align: left;
        }

        .col-effectif .col-etrangers{
            width: 25%;
            text-align: center;
        }

        .grid-table td.col-effectif,
        .grid-table td.col-etrangers {
            border: 1px solid #000;
        }

        /*.col-etrangers {
            width: 22%;
            text-align: center;
        }*/

        .grid-header {
            background-color: transparent;
            font-weight: bold;
            text-align: center;
        }

        .col-fonction {
            width: 40%;
        }

        .col-effectif {
            width: 30%;
            text-align: center;
        }

        .col-etrangers {
            width: 30%;
            text-align: center;
        }

        .grid-header {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .radio-circle {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            border-radius: 50%;
            text-align: center;
            line-height: 8px;
            font-size: 24px;
            vertical-align: middle;
            margin: 0 4px 0 14px;
        }

        .footnote {
            font-size: 9px;
            margin-top: 8px;
        }

        .signature-box {
            border: 1px solid #000;
            min-height: 95px;
            margin-top: 2px;
        }

        .portal-footer {
            font-size: 9px;
            margin-top: 6px;
        }

        @stack('styles')
    </style>
</head>
<body>
<div class="container">

    @php
        $domaines = array_pad($organisme->domaines_competence ?? [], 3, '');
        $moyens = array_pad($organisme->moyens_pedagogiques ?? [], 3, '');
    @endphp

    <div class="csf-header-label">Contrats Spéciaux de Formation</div>
    <div class="csf-header-title">Formulaire F3</div>
    <div class="csf-header-bar"></div>
    <div class="document-heading">Fiche d'identification de l'Organisme de Formation</div>

    {{-- Identité de l'organisme --}}
    <div class="form-box clearfix">
        <div class="field-label">Raison sociale:</div>
        <div class="field-input">{{ $organisme->raison_sociale }}</div>

        <div class="clearfix">
            <div class="col-50">
                <div class="field-label">Forme juridique*:</div>
                <div class="field-input">{{ $organisme->statut_juridique }}</div>
            </div>
            <div class="col-50">
                <div class="field-label">Date de création:</div>
                <div class="field-input date-field">
                    {{ optional($organisme->date_creation)->format('d/m/Y') }}
                    <span class="calendar-icon"></span>
                </div>
            </div>
        </div>

        <div class="field-label">Nom et prénom du gérant:</div>
        <div class="field-input">{{ $organisme->gerant_prenom }} {{ $organisme->gerant_nom }}</div>

        <div class="field-label">Adresse:</div>
        <div class="field-input">{{ $organisme->siege_social }}</div>

        <div class="inline-row clearfix">
            <span class="inline-label-left">Téléphone :</span>
            <span class="inline-box-tel">{{ $organisme->telephone }}</span>
            <span class="inline-label-left">Fax :</span>
            <span class="inline-box-tel">{{ $organisme->fax }}</span>
        </div>

        <div class="inline-row clearfix">
            <span class="inline-label-left">Email :</span>
            <span class="inline-box-email">{{ $organisme->email }}</span>
        </div>
    </div>

    {{-- Identifiants légaux --}}
    <div class="form-box">
        <div class="clearfix" style="margin-bottom: 8px;">
            <div class="col-50">
                <span class="inline-label-right">Patente:</span>
                <span class="inline-box">{{ $organisme->patente }}</span>
            </div>
            <div class="col-50">
                <span class="inline-label-right">Identifiant fiscal:</span>
                <span class="inline-box">{{ $organisme->if }}</span>
            </div>
        </div>
        <div class="clearfix">
            <div class="col-50">
                <span class="inline-label-right">N° RC:</span>
                <span class="inline-box">{{ $organisme->rc }}</span>
            </div>
            <div class="col-50">
                <span class="inline-label-right">N° CNSS:</span>
                <span class="inline-box">{{ $organisme->cnss }}</span>
            </div>
        </div>
    </div>

    {{-- Domaines de compétence / Moyens matériels --}}
    <div class="form-box">
        <div class="clearfix">
            <div class="col-50">
                <div class="field-label">Domaines de compétence:</div>
            </div>
            <div class="col-50">
                <div class="field-label">Moyens matériels pédagogiques:</div>
            </div>
        </div>
        @for ($i = 0; $i < 3; $i++)
            <div class="clearfix">
                <div class="col-50">
                    <div class="field-input small-input">{{ $domaines[$i] }}</div>
                </div>
                <div class="col-50">
                    <div class="field-input small-input">{{ $moyens[$i] }}</div>
                </div>
            </div>
        @endfor
    </div>

    {{-- Moyens humains --}}
    <div class="form-box">
        <div class="field-label" style="margin-bottom: 4px;">Moyens humains de l'Organisme :</div>
        <table class="grid-table">
            <thead>
            <tr>
                <th class="grid-header col-fonction">Fonction</th>
                <th class="grid-header col-effectif">Effectif total (actuel)</th>
                <th class="grid-header col-etrangers">dont étrangers</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="col-fonction">Consultants/Experts permanents</td>
                <td class="col-effectif">{{ $organisme->nb_experts_permanents }}</td>
                <td class="col-etrangers">{{ $organisme->nb_experts_permanents_etrangers }}</td>
            </tr>
            <tr>
                <td class="col-fonction">Consultants/Experts vacataires</td>
                <td class="col-effectif">{{ $organisme->nb_experts_vacataires }}</td>
                <td class="col-etrangers">{{ $organisme->nb_experts_vacataires_etrangers }}</td>
            </tr>
            <tr>
                <td class="col-fonction">Animateurs/Formateurs</td>
                <td class="col-effectif">{{ $organisme->nb_animateurs_formateurs }}</td>
                <td class="col-etrangers">{{ $organisme->nb_animateurs_formateurs_etrangers }}</td>
            </tr>
            <tr>
                <td class="col-fonction">Autres employés</td>
                <td class="col-effectif">{{ $organisme->nb_autres_employes }}</td>
                <td class="col-etrangers">{{ $organisme->nb_autres_employes_etrangers }}</td>
            </tr>
            <tr>
                <td class="col-fonction"><strong>Total</strong></td>
                <td class="col-effectif"><strong>{{ $organisme->effectif_total }}</strong></td>
                <td class="col-etrangers"><strong>{{ $organisme->effectif_total_etrangers }}</strong></td>
            </tr>
            </tbody>
        </table>
    </div>

    {{-- Groupe étranger --}}
    <div class="form-box">
        L'organisme appartient-il à un groupe étranger ?
        <span class="radio-circle">@if($organisme->appartient_groupe_etranger) &#8226; @endif</span> Oui
        <span class="radio-circle">@unless($organisme->appartient_groupe_etranger) &#8226; @endunless</span> Non
    </div>

    {{-- Fait à / Signature --}}
    <div class="form-box clearfix">
        <div class="col-50">
            <div class="field-label">Fait à:</div>
            <div class="field-input">{{ $organisme->ville }}</div>

            <div class="field-label">Nom et prénom:</div>
            <div class="field-input">{{ $organisme->gerant_prenom }} {{ $organisme->gerant_nom }}</div>

            <div class="field-label">Qualité:</div>
            <div class="field-input">{{ $organisme->representant_fonction }}</div>

            <div class="footnote">* Pour les personnes physiques, joindre une attestation d'inscription au rôle des Patentes.</div>
        </div>

        <div class="col-50">
            <div class="field-label">Le:</div>
            <div class="field-input date-field">
                {{ $dateEdition->format('d/m/Y') }}
                <span class="calendar-icon"></span>
            </div>

            <div class="field-label">Signature et cachet de l'Organisme:</div>
            <div class="signature-box"></div>
        </div>
    </div>

    <div class="portal-footer">
        Ce formulaire est disponible sur le Portail des CSF à l'adresse: http://csf.ofppt.org.ma.<br>
        Il peut être rempli sur l'écran en tant que formulaire PDF avant d'être imprimé.
    </div>

</div>
</body>
</html>
