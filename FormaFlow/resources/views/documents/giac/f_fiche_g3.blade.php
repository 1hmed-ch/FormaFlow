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

        /*
           You can leave this commented if you are printing literal dots,
           or uncomment it if you want CSS to draw the lines for you.
        */
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
            display: inline-flex;
            justify-content: center;
            width: 26px;
            height: 16px;
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
                Raison sociale : <span class="dotted-fill wide">{{ $organisme->raison_sociale ?: '.........................................................................................' }}</span>
            </td>
        </tr>

        <tr>
            <td style="width: 55%; padding: 5px 0; vertical-align: bottom;">
                Forme juridique : <span class="dotted-fill">{{ $organisme->statut_juridique ?: '......................................' }} <span style="margin-left: 10px;">;</span></span>
            </td>
            <td style="width: 45%; padding: 5px 0; vertical-align: bottom;">
                Date de création : <span class="dotted-fill">{{ optional($organisme->date_creation)->format('d/m/Y') ?: '......../......../..............' }}</span>
            </td>
        </tr>

        <tr>
            <td style="width: 55%; padding: 5px 0; vertical-align: bottom;">
                Patente : <span class="dotted-fill">{{ $organisme->patente ?: '......................................' }} <span style="margin-left: 10px;">;</span> </span>
            </td>
            <td style="width: 45%; padding: 5px 0; vertical-align: bottom;">
                Identifiant fiscal : <span class="dotted-fill">{{ $organisme->if ?: '......................................' }}</span>
            </td>
        </tr>

        <tr>
            <td style="width: 55%; padding: 5px 0; vertical-align: bottom;">
                N° RC : <span class="dotted-fill">{{ $organisme->rc ?: '......................................' }} <span style="margin-left: 10px;">;</span> </span>
            </td>
            <td style="width: 45%; padding: 5px 0; vertical-align: bottom;">
                N° CNSS : <span class="dotted-fill">{{ $organisme->cnss ?: '......................................' }}</span>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="padding: 5px 0; vertical-align: bottom;">
                Adresse : <span class="dotted-fill wide">{{ $organisme->siege_social ?: '.........................................................................................' }}</span>
            </td>
        </tr>

        <tr>
            <td style="width: 55%; padding: 5px 0; vertical-align: bottom;">
                Tél. : <span class="dotted-fill">{{ $organisme->telephone ?: '.................................' }} <span style="margin-left: 10px;">;</span> </span>
            </td>
            <td style="width: 45%; padding: 5px 0; vertical-align: bottom;">
                Fax : <span class="dotted-fill">{{ $organisme->fax ?: '.................................' }}</span>
            </td>
        </tr>

        <tr>
            <td style="width: 55%; padding: 5px 0; vertical-align: bottom;">
                Email : <span class="dotted-fill">{{ $organisme->email ?: '.................................' }} <span style="margin-left: 10px;">;</span> </span>
            </td>
            <td style="width: 45%; padding: 5px 0; vertical-align: bottom;">
                Site Web : <span class="dotted-fill">{{ $organisme->site_web ?: '......................................' }}</span>
            </td>
        </tr>
    </table>

    <div class="field-line">
        Nom et prénom du gérant :
        <span class="dotted-fill wide">{{ trim(($organisme->gerant_prenom ?? '') . ' ' . ($organisme->gerant_nom ?? '')) ?: '......................................................................................' }}</span>
    </div>

    <div class="field-line">
        Nom et prénom du chargé de mission :
        <span class="dotted-fill wide">{{ $organisme->representant_nom ?: '......................................................................................' }}</span>
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
                <td>{{ is_numeric($organisme->nb_experts_permanents) ? $organisme->nb_experts_permanents : '..........' }}</td>
                <td>{{ is_numeric($organisme->nb_experts_permanents_etrangers) ? $organisme->nb_experts_permanents_etrangers : '..........' }}</td>
            </tr>
            <tr>
                <td>Consultants/Experts vacataires</td>
                <td>{{ is_numeric($organisme->nb_experts_vacataires) ? $organisme->nb_experts_vacataires : '..........' }}</td>
                <td>{{ is_numeric($organisme->nb_experts_vacataires_etrangers) ? $organisme->nb_experts_vacataires_etrangers : '..........' }}</td>
            </tr>
            <tr>
                <td>Animateurs/Formateurs</td>
                <td>{{ is_numeric($organisme->nb_animateurs_formateurs) ? $organisme->nb_animateurs_formateurs : '..........' }}</td>
                <td>{{ is_numeric($organisme->nb_animateurs_formateurs_etrangers) ? $organisme->nb_animateurs_formateurs_etrangers : '..........' }}</td>
            </tr>
            <tr>
                <td>Autres employés :</td>
                <td>{{ is_numeric($organisme->nb_autres_employes) ? $organisme->nb_autres_employes : '..........' }}</td>
                <td>{{ is_numeric($organisme->nb_autres_employes_etrangers) ? $organisme->nb_autres_employes_etrangers : '..........' }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td colspan="2"><strong>{{ is_numeric($organisme->effectif_total) ? $organisme->effectif_total : '..........' }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="field-line">
        L'organisme appartient-il à un groupe étranger ?
        &nbsp;&nbsp;&nbsp;
        <span style="margin-right: 6px;">oui</span>
        <span class="oval" style="font-size: 36px;">{!! $organisme->appartient_groupe_etranger ? '&bull;' : '' !!}</span>
        &nbsp;&nbsp;&nbsp;
        <span style="margin-right: 6px;">non</span>
        <span class="oval" style="font-size: 36px;">{!! $organisme->appartient_groupe_etranger === false ? '&bull;' : '' !!}</span>
    </div>

    <div class="field-line">
        Si oui lequel :
        <span class="dotted-fill wide">{{ $organisme->nom_groupe_etranger ?? '..............................................................................................' }}</span>
    </div>

    <div class="gbb-box" style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px 10px; margin: 4px -10px;">
        <div class="field-line">
            Références :
            <span class="dotted-fill wide"> {!! !is_null($organisme->references) ? $organisme->references : '...........................................................................................................................................................' !!}</span>
        </div>
        <div class="field-line">
            <span class="dotted-fill wide"> {!! !is_null($organisme->references) ? $organisme->references : '..................................................................................................................................................................................' !!}</span>
        </div>
        <div class="field-line">
            <span class="dotted-fill wide"> {!! !is_null($organisme->references) ? $organisme->references : '..................................................................................................................................................................................' !!}</span>
        </div>
    </div>
    <div class="signature-zone right" style="margin-top: 16px;">
        Fait à : <span class="dotted-fill">{{ $organisme->ville ?: '.........................' }}</span>
        &nbsp; ; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        Le : <span class="dotted-fill">{{ isset($dateEdition) ? $dateEdition->format('d/m/Y') : '...../...../........' }}</span>
    </div>
    <div class="signature-zone centered">
        <div class="field-line" style="margin-top: 16px;"> {{ trim(($organisme->gerant_nom ?? '') . ' ' . ($organisme->gerant_prenom ?? '')) ?: '.......................................' }}</div>
        <div class="field-line">Qualité du signataire</div>
        <div class="field-line">Signature et Cachet de l'Organisme</div>
    </div>
</div>

<x-giac-footer />

</body>
</html>
