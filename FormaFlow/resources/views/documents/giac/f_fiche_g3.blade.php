<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>G3 - Fiche de Renseignement de l'Organisme de Conseil</title>
    <style>
        @page {
            margin: 40px 60px 90px 60px;
        }

        @font-face {
            font-family: 'Lucida Handwriting';
            src: url("{{ public_path('fonts/External/LucidaHandwritingStdBold.TTF') }}") format('truetype');
            font-style: italic;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            font-style: normal;
            color: #1a1a1a;
            line-height: 1.45;
        }

        .giac-header-logo {
            text-align: center;
            margin-bottom: 2px;
        }

        .giac-header-logo img {
            height: 120px;
        }

        .giac-header-rule {
            border: none;
            border-top: 1px solid #A3001E;
            width: 180px;
            margin: 2px auto 4px auto;
        }

        .giac-header-text {
            text-align: center;
            font-style: italic;
            font-size: 17px;
            margin-bottom: 14px;
        }

        .document-title-plain {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 19px;
            margin-bottom: 16px;
        }

        .field-line {
            margin: 5px 0;
        }

        /*.dotted-fill {
            border-bottom: 1px dotted #333;
            display: inline-block;
            min-width: 140px;
            padding: 0 4px;
        }

        .dotted-fill.wide {
            min-width: 100%;
            width: 100%;
        }*/

        .g-box {
            border: 1px solid #000;
            padding: 8px 10px;
            margin-bottom: 10px;
        }

        .g-box-title {
            font-weight: bold;
            margin-bottom: 6px;
        }

        .oval {
            display: inline-block;
            width: 26px;
            height: 14px;
            border: 1px solid #000;
            border-radius: 50%;
            vertical-align: middle;
            text-align: center;
            line-height: 12px;
            font-size: 10px;
            font-weight: bold;
        }

        table.effectif-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        table.effectif-table th,
        table.effectif-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        table.effectif-table tr > *:first-child {
            border-left: none;
        }

        table.effectif-table tr > *:last-child {
            border-right: none;
        }

        table.effectif-table th {
            text-align: center;
        }

        table.effectif-table td:nth-child(2),
        table.effectif-table td:nth-child(3) {
            text-align: center;
            width: 120px;
        }

        .signature-zone {
            margin-top: 20px;
        }

        .signature-zone.centered {
            text-align: center;
        }

        .signature-zone.right {
            text-align: right;
        }

        .signature-zone .field-line {
            margin: 6px 0;
        }

        .giac-footer {
            position: fixed;
            bottom: -70px;
            left: -60px;
            right: -60px;
            text-align: center;
            border-top: 4px double #622423;
            padding-top: 4px;
            font-family: 'Times New Roman', Times, serif;
        }

        .giac-footer .footer-brand {
            font-family: 'Lucida Handwriting', cursive;
            font-style: italic;
            font-size: 13px;
        }

        .giac-footer .footer-line2 {
            font-weight: bold;
            font-style: italic;
            font-size: 12px;
            margin-top: 2px;
        }

        .giac-footer a {
            color: #0563C1;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="giac-header-logo">
    <img src="{{ $giacLogo }}">
    <div class="giac-header-rule"></div>
</div>
<div class="giac-header-text">Groupement Interprofessionnel d'Aide au Conseil</div>

<div class="document-title-plain">G3 : FICHE DE RENSEIGNEMENT DE L'ORGANISME DE CONSEIL</div>
<div class="g-box">

    <table style="width: 100%; border-collapse: collapse; border: none;">
        <tr>
            <td colspan="2" style="padding: 5px 0; vertical-align: bottom;">
                Raison sociale : <span class="dotted-fill wide">{{ $organisme->raison_sociale }}</span>
            </td>
        </tr>

        <tr>
            <td style="width: 55%; padding: 5px 0; vertical-align: bottom;">
                Forme juridique : <span class="dotted-fill">{{ $organisme->statut_juridique }}</span>
            </td>
            <td style="width: 45%; padding: 5px 0; vertical-align: bottom;">
                Date de création : <span class="dotted-fill">{{ optional($organisme->date_creation)->format('d/m/Y') }}</span>
            </td>
        </tr>

        <tr>
            <td style="width: 55%; padding: 5px 0; vertical-align: bottom;">
                Patente : <span class="dotted-fill">{{ $organisme->patente }}</span>
            </td>
            <td style="width: 45%; padding: 5px 0; vertical-align: bottom;">
                Identifiant fiscal : <span class="dotted-fill">{{ $organisme->if }}</span>
            </td>
        </tr>

        <tr>
            <td style="width: 55%; padding: 5px 0; vertical-align: bottom;">
                N° RC : <span class="dotted-fill">{{ $organisme->rc }}</span>
            </td>
            <td style="width: 45%; padding: 5px 0; vertical-align: bottom;">
                N° CNSS : <span class="dotted-fill">{{ $organisme->cnss }}</span>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="padding: 5px 0; vertical-align: bottom;">
                Adresse : <span class="dotted-fill wide">{{ $organisme->siege_social }}</span>
            </td>
        </tr>

        <tr>
            <td style="width: 55%; padding: 5px 0; vertical-align: bottom;">
                Tél. : <span class="dotted-fill">{{ $organisme->telephone }}</span>
            </td>
            <td style="width: 45%; padding: 5px 0; vertical-align: bottom;">
                Fax : <span class="dotted-fill">{{ $organisme->fax }}</span>
            </td>
        </tr>

        <tr>
            <td style="width: 55%; padding: 5px 0; vertical-align: bottom;">
                Email : <span class="dotted-fill">{{ $organisme->email }}</span>
            </td>
            <td style="width: 45%; padding: 5px 0; vertical-align: bottom;">
                Site Web : <span class="dotted-fill">{{ $organisme->site_web }}</span>
            </td>
        </tr>
    </table>

    <div class="field-line">
        Nom et prénom du gérant :
        <span class="dotted-fill wide">{{ $organisme->gerant_prenom }} {{ $organisme->gerant_nom }}</span>
    </div>

    <div class="field-line">
        Nom et prénom du chargé de mission :
        <span class="dotted-fill wide">{{ $organisme->representant_nom }}</span>
    </div>

    <div class="field-line" style="border-top: 1px solid #000; padding: 4px 10px; margin: 2px -10px;">
        Moyens humains de l’organisme :
    </div>

    <div class="giac-box" style="margin-top: 2px; padding: 0;margin-left: -10px; margin-right: -10px;">
        <table class="effectif-table">
            <tr>
                <th style="text-align: left;">Fonction</th>
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
                <td>Autres employés :</td>
                <td>{{ $organisme->nb_autres_employes }}</td>
                <td>{{ $organisme->nb_autres_employes_etrangers }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td colspan="2"><strong>{{ $organisme->effectif_total }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="field-line">
        L'organisme appartient-il à un groupe étranger ?
        &nbsp;&nbsp;&nbsp;
        <span style="margin-right: 6px;">oui</span>
        <span class="oval">{{ $organisme->appartient_groupe_etranger ? 'X' : '' }}</span>
        &nbsp;&nbsp;&nbsp;
        <span style="margin-right: 6px;">non</span>
        <span class="oval">{{ $organisme->appartient_groupe_etranger ? '' : 'X' }}</span>
    </div>

    <div class="field-line">
        Si oui lequel :
        <span class="dotted-fill wide">&nbsp;</span>
    </div>

    <div class="gbb-box" style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px 10px; margin: 4px -10px;">
        <div class="field-line">
            Références :
            <span class="dotted-fill wide">&nbsp;</span>
        </div>
        <div class="field-line">
            <span class="dotted-fill wide">&nbsp;</span>
        </div>
        <div class="field-line">
            <span class="dotted-fill wide">&nbsp;</span>
        </div>
    </div>
    <div class="signature-zone right" style="margin-top: 16px;">
        Fait à : <span class="dotted-fill">{{ $organisme->siege_social }}</span>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        Le : <span class="dotted-fill">{{ $dateEdition->format('d/m/Y') }}</span>
    </div>
    <div class="signature-zone centered">
        <div class="field-line" style="margin-top: 16px;"> {{ $organisme->gerant_nom }} {{ $organisme->gerant_prenom }}</div>
        <div class="field-line">Qualité du signataire</div>
        <div class="field-line">Signature et Cachet de l'Organisme</div>
    </div>
</div>
<div class="giac-footer">
    <div class="field-line">
        <span class="footer-brand">GIAC Technologies</span>
        <span class="footer-line2">- 2 Rue Abou Said Assoussi, Résidence El Fariss, 1<sup>er</sup> étage, Appartement n° 9, Casablanca</span>
    </div>
    <div class="field-line footer-line2">
        Tél. : 0522 27 24 93 &ndash; Fax : 0522 27 57 65 &ndash; CNSS : 7365514 &ndash; e-mail :
        <a href="mailto:giactechnologies@gmail.com">giactechnologies@gmail.com</a>
        &ndash; web : <a href="http://www.giactechnologies.com">www.giactechnologies.com</a>
    </div>
</div>

</body>
</html>
