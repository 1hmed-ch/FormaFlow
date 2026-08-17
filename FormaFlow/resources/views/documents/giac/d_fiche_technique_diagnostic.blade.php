<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>D - Fiche Technique de l'Etude du Diagnostic Stratégique</title>
    <style>
        @page {
            margin: 40px 70px 90px 70px;
        }

        @font-face {
            font-family: 'Lucida Handwriting';
            src: url("{{ public_path('fonts/External/LucidaHandwritingStdBold.TTF') }}") format('truetype');
            font-style: italic;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            font-style: italic;
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

        .document-title-box {
            border: 1.5px solid #000;
            background-color: #FADAE1;
            padding: 8px 10px;
            text-align: center;
            font-weight: bold;
            font-style: italic;
            font-size: 18px;
            margin: 16px 110px;
        }

        .document-title-box .subtitle {
            display: block;
            font-size: 18px;
        }

        .field-line {
            margin: 5px 10px;
        }

        .field-label {
            font-weight: bold;
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
            text-align: center;
            border-bottom: 1px solid #000;
            margin: -8px -10px 8px -10px;
            padding: 5px 10px;
        }

        .g-box-caption {
            font-style: italic;
            font-weight: normal;
            font-size: 11px;
            text-align: center;
            display: block;
        }

        .signature-zone {
            margin-top: 20px;
        }

        .signature-zone .field-line {
            margin: 6px 0;
        }

        /*.giac-footer {
            position: fixed;
            bottom: -70px;
            left: -65px;
            right: -65px;
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
        }*/
    </style>
</head>
<body>

<div class="giac-header-logo">
    <img src="{{ $giacLogo }}">
    <div class="giac-header-rule"></div>
</div>
<div class="giac-header-text">Groupement Interprofessionnel d'Aide au Conseil</div>

<div class="document-title-box">
    D) Fiche Technique de l'Etude
    <span class="subtitle">du Diagnostic Stratégique</span>
</div>

<div class="field-line">
    <span class="field-label" style="text-decoration: underline; margin-left: 50px;">ENTREPRISE BENEFICIAIRE</span> :
    <span class="dotted-fill wide">{{ $entreprise->raison_sociale ?: '.............................................' }}</span>
</div>

<div class="g-box">
    <div class="g-box-title">NATURE du PROJET de DEVELOPPEMENT de l'ENTREPRISE</div>

    <div class="field-line">
        <strong>- Marché d'Exportation : </strong>
        <span class="dotted-fill">{{ $etude->projet_marche_export ? 'Oui' : '..............' }}</span>
        <strong style="margin-left: 80px;">- Investissement Technologique : </strong>
        <span class="dotted-fill">{{ $etude->projet_investissement_techno ? 'Oui' : '..............' }}</span>
    </div>
    <div class="field-line">
        <strong>- Mise aux Normes : </strong>
        <span class="dotted-fill">{{ $etude->projet_mise_aux_normes ? 'Oui' : '..............' }}</span>
        <strong style="margin-left: 100px;">- Autres a préciser : </strong>
        <span class="dotted-fill wide">{{ $etude->projet_autre ? ($etude->projet_autre_precision ?: '..............................') : '..............................' }}</span>
    </div>
    <span>{{ $etude->projet_autre_precision ?? '.......................................................................................................................................................................' }}</span>
</div>

<div class="g-box">
    <div class="g-box-title">OBJECTIFS et RESULTATS ATTENDUS du DIAGNOSTIC :</div>
    <div class="field-line">{{ $etude->objectifs_resultats_attendus ?: '......................................................................' }}</div>
</div>

<div class="g-box">
    <div class="g-box-title">
        PROPOSITION d'INTERVENTION du CABINET-CONSEIL :
        <span class="g-box-caption">( Joindre pour détails , l'offre soumise par ce Cabinet )</span>
    </div>

    <div class="field-line">
        <strong>- Prestations Envisagées :</strong>
        {{ $etude->prestations_envisagees ?: '..................................................................' }}
    </div>
    <div class="field-line">
        <strong>- En vue de leur application durant l'année :</strong>
        <span class="dotted-fill">{{ $etude->annee_application ?: '..........' }}</span>
    </div>
    <div class="field-line">
        <strong>- Durée Effective de l'Intervention ( nombre de jours ) :</strong>
        <span class="dotted-fill">{{ is_numeric($etude->duree_intervention_jours) ? $etude->duree_intervention_jours : '..........' }}</span>
    </div>
    <div class="field-line">
        <strong>- Date de démarrage :</strong>
        <span class="dotted-fill">{{ optional($etude->date_demarrage)->format('d/m/Y') ?: '...../...../..........' }}</span>
    </div>
    <div class="field-line">
        <strong>- Coût Prévisionnel de l'Intervention ( en DH , H.T. ) :</strong>
        <span class="dotted-fill">{{ is_numeric($etude->cout_previsionnel) ? number_format((float) $etude->cout_previsionnel, 2, ',', ' ') . ' DH H.T' : '.................................' }}</span>
    </div>

    <div class="g-box-title" style="margin-top: 8px; border-top: 1px solid #000;">CABINET &ndash; CONSEIL chargé de l'INTERVENTION :</div>

    <div class="field-line">
        <strong>- Raison Sociale :</strong>
        <span class="dotted-fill wide">{{ $organisme->raison_sociale ?: '............................................................................' }}</span>
    </div>
    <div class="field-line">
        <strong>- Adresse :</strong>
        <span class="dotted-fill wide">{{ $organisme->siege_social ?: '.............................................................' }}</span>
    </div>
    <div class="field-line">
        <strong>- N° de CNSS :</strong>
        <span class="dotted-fill">{{ $organisme->cnss ?: '.......................' }}</span>
        <strong style="margin-left: 35px;">- Téléphone :</strong>
        <span class="dotted-fill">{{ $organisme->telephone ?: '.......................' }}</span>
        <strong style="margin-left: 35px;">- Fax :</strong>
        <span class="dotted-fill">{{ $organisme->fax ?: '.......................' }}</span>
    </div>
    <div class="field-line">
        <strong>- N° R.C. :</strong>
        <span class="dotted-fill">{{ $organisme->rc ?: '......................................' }}</span>
        <strong style="margin-left: 130px;">- Mail :</strong>
        <span class="dotted-fill">{{ $organisme->email ?: '......................................' }}</span>
    </div>
    <div class="field-line">
        <strong>- Responsable Principal à contacter :</strong>
        <span class="dotted-fill wide">{{ $organisme->representant_nom ?: '..................................................' }}</span>
    </div>
</div>

<div class="signature-zone">
    <div class="field-line"><strong>- Date :</strong> {{ isset($dateEdition) ? $dateEdition->format('d/m/Y') : '......../......../..............' }}</div>
    <div class="field-line"><strong>- Cachet et Signature :</strong></div>
</div>


<x-giac-footer />

</body>
</html>
