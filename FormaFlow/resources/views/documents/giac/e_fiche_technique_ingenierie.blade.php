<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>E - Fiche Technique de l'Etude d'Ingénierie de Formation</title>
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
            font-family: 'Times New Roman', Times, serif;
            font-size: 16px;
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
            margin: 16px 100px;
        }

        .document-title-box .subtitle {
            display: block;
            font-size: 18px;
        }

        .field-line {
            margin: 5px 12px;
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
            margin: 15px 0;
        }

        .signature-zone {
            margin-top: 1px;
        }

        .signature-zone .field-line {
            margin: 6px 0;
        }

        /*.giac-footer {
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
    E) Fiche Technique de l'Etude
    <span class="subtitle">d'Ingénierie de Formation</span>
</div>

<div class="field-line">
    <span class="field-label" style="text-decoration: underline; margin-left: 30px">ENTREPRISE BENEFICIAIRE</span> :
    <span class="dotted-fill wide">{{ $entreprise->raison_sociale ?: '.........................................................................................' }}</span>
</div>

<div class="g-box">
    <div class="field-line">
        <strong>1) Nature de l'Action :</strong>
        {{ $etude->nature_action ?: '..................................................' }}
    </div>

    <div class="field-line">
        <strong>- Diagnostic des Besoins en Formation :</strong>
        {{ $etude->diagnostic_besoins ?: '............................................................................................................................................................' }}
    </div>

    <div class="field-line">
        <strong>- Elaboration d'un Plan de Formation :</strong>
        <span class="dotted-fill">{{ $etude->plan_formation ? 'Oui' : 'Non' }}</span>
        <strong style="margin-left: 60px">Pour l'Année :</strong>
        <span class="dotted-fill">{{ $etude->plan_formation ? ($etude->plan_formation_annee ?: '..........') : '...........' }}</span>
    </div>

    <div class="field-line">
        <strong>- Bilan de Compétence :</strong> {{ $etude->bilan_competence ?: '....................................................................................................................' }}
    </div>

    <div class="field-line">
        <strong>- Autres ( à préciser ) :</strong> {{ $etude->autres_precisions ?: '....................................................................................................................' }}
    </div>

    <div class="field-line">
        <strong>2) Résultats attendus de l'Action :</strong>
        {{ $etude->resultats_attendus ?: '......................................................................................' }}
    </div>

    <div class="field-line">
        <strong>3) Période de Réalisation :</strong>
        <strong style="margin: 0 7px">du</strong> {{ optional($etude->periode_debut)->format('d/m/Y') ?: '...../...../.........' }}
        <strong style="margin: 0 7px">au</strong> {{ optional($etude->periode_fin)->format('d/m/Y') ?: '...../...../........' }}
    </div>

    <div class="field-line">
        <strong>4) Nombre de jours d'Intervention :</strong>
        {{ is_numeric($etude->nb_jours_intervention) ? $etude->nb_jours_intervention : '..........' }}
    </div>

    <div class="field-line">
        <strong>5) Organisme d'Intervention :</strong>
        {{ $organisme->raison_sociale ?: '......................................................' }}
    </div>
    <div class="field-line" style="margin-left: 45px"><strong>* Adresse :</strong> {{ $organisme->siege_social ?: '...........................................................' }}</div>
    <div class="field-line" style="margin-left: 45px">
        <strong>* N° de CNSS :</strong> {{ $organisme->cnss ?: '.......................' }}
        &nbsp;&nbsp; <strong style="margin-left: 80px">Mail :</strong> {{ $organisme->email ?: '......................................' }}
    </div>
    <div class="field-line" style="margin-left: 45px">
        <strong>* Tel. :</strong> {{ $organisme->telephone ?: '.......................' }}
        &nbsp;&nbsp; <strong style="margin-left: 145px">Fax :</strong> {{ $organisme->fax ?: '......................................' }}
    </div>
    <div class="field-line" style="margin-left: 45px"><strong>* R.C. :</strong> {{ $organisme->rc ?: '......................................' }}</div>
    <div class="field-line" style="margin-left: 45px">
        <strong>* Personne(s) à contacter :</strong> {{ $organisme->representant_nom ?: '............................................................' }}
    </div>
    <div class="field-line" style="margin-left: 45px">
        <strong>* Fonction dans l'Entreprise :</strong> {{ $organisme->representant_fonction ?: '......................................' }}
    </div>

    <div class="field-line">
        <strong>6) Proposition d'Intervention du Prestataire</strong>
        ( à joindre en annexe de cette fiche )
    </div>

    <div class="field-line">
        <strong>7) Coût de l'Action ( Hors Taxe ) :</strong>
        {{ is_numeric($etude->cout_action) ? number_format((float) $etude->cout_action, 2, ',', ' ') . ' DH' : '......................................' }}
    </div>
</div>

<div class="signature-zone">
    <div class="field-line"><strong>- Date :</strong> {{ isset($dateEdition) ? $dateEdition->format('d/m/Y') : '...../...../.........' }}</div>
    <div class="field-line"><strong>- Cachet et Signature :</strong></div>
</div>

<x-giac-footer />
</body>
</html>
