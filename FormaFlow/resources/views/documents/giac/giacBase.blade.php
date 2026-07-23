<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('documentTitle', 'Document GIAC')</title>
    <style>
        /*
         * Gabarit partagé des documents GIAC (G3, G4, G6, G7).
         *
         * Calqué visuellement sur les modèles Word officiels : logo +
         * intitulé en en-tête, titre encadré fond rose (ou titre simple
         * souligné pour G3), pied de page GIAC Technologies identique sur
         * toutes les pages, boîtes à bordure noire pour chaque section.
         *
         * Cambria / Lucida Handwriting (polices Word d'origine) ne sont
         * pas garanties disponibles côté serveur : on utilise Times New
         * Roman (proche de Cambria) comme police de substitution sûre
         * pour dompdf.
         */
        @page {
            margin: 40px 45px 90px 45px;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #1a1a1a;
            line-height: 1.45;
        }

        .giac-italic {
            font-style: italic;
        }

        .giac-header {
            width: 100%;
            margin-bottom: 10px;
        }

        .giac-logo {
            height: 60px;
        }

        .giac-header-text {
            font-size: 13px;
            font-style: italic;
            text-align: center;
            padding-top: 4px;
        }

        /* Titre encadré, fond rose clair (B-2, D, E) */
        .document-title-box {
            border: 1px solid #000;
            background-color: #FADAE1;
            padding: 8px 10px;
            text-align: center;
            font-weight: bold;
            font-style: italic;
            font-size: 15px;
            margin-bottom: 14px;
        }

        .document-title-box .subtitle {
            display: block;
            font-size: 15px;
        }

        .document-title-box .caption {
            display: block;
            font-size: 11px;
            font-weight: normal;
        }

        /* Titre simple, souligné, sans encadré (G3) */
        .document-title-plain {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 14px;
            margin-bottom: 14px;
        }

        .field-line {
            margin: 5px 0;
        }

        .field-label {
            font-weight: bold;
        }

        .dotted-fill {
            border-bottom: 1px dotted #333;
            display: inline-block;
            min-width: 140px;
            padding: 0 4px;
        }

        .dotted-fill.wide {
            min-width: 100%;
            width: 100%;
        }

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

        /* Ovale à cocher (oui / non), tel que dans le modèle G3 d'origine */
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

        table.effectif-table th {
            text-align: center;
        }

        table.effectif-table td:nth-child(2),
        table.effectif-table td:nth-child(3) {
            text-align: center;
            width: 90px;
        }

        .signature-zone {
            margin-top: 30px;
        }

        .signature-zone .field-line {
            margin: 8px 0;
        }

        .signature-zone.centered {
            text-align: center;
        }

        /* Pied de page GIAC Technologies, identique sur chaque page générée
           (position fixed = répété automatiquement par dompdf). */
        .giac-footer {
            position: fixed;
            bottom: -70px;
            left: 0;
            right: 0;
            text-align: center;
            border-top: 3px double #622423;
            padding-top: 4px;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
            font-style: italic;
            font-size: 11px;
        }

        .giac-footer .footer-line2 {
            font-size: 10px;
        }

        .giac-footer a {
            color: #000;
        }

        @stack('styles')
    </style>
</head>
<body>

<table class="giac-header">
    <tr>
        <td style="width: 90px; vertical-align: top;">
            <img src="{{ $giacLogo }}" class="giac-logo">
        </td>
        <td style="vertical-align: top; padding-left: 10px;">
            <div class="giac-header-text">Groupement Interprofessionnel d'Aide au Conseil</div>
        </td>
    </tr>
</table>

@hasSection('documentTitlePlain')
    <div class="document-title-plain">@yield('documentTitlePlain')</div>
@else
    <div class="document-title-box">
        @yield('documentTitle')
        @hasSection('documentSubtitle')
            <span class="subtitle">@yield('documentSubtitle')</span>
        @endif
        @hasSection('documentCaption')
            <span class="caption">@yield('documentCaption')</span>
        @endif
    </div>
@endif

@yield('content')

<div class="giac-footer">
    <div class="field-line">GIAC Technologies - 2 Rue Abou Said Assoussi, Résidence El Fariss, 1<sup>er</sup> étage, Appartement n° 9, Casablanca</div>
    <div class="field-line footer-line2">Tél. : 0522 27 24 93 &ndash; Fax : 0522 27 57 65 &ndash; CNSS : 7365514 &ndash; e-mail : <a href="mailto:giactechnologies@gmail.com">giactechnologies@gmail.com</a> &ndash; web : <a href="http://www.giactechnologies.com">www.giactechnologies.com</a></div>
</div>

</body>
</html>
