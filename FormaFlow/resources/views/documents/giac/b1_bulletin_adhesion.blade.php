<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>B-1 - Bulletin d'Adhésion</title>
    <style>
     @page {
    margin: 15px 30px 45px 30px;
}

        @font-face {
            font-family: 'Lucida Handwriting';
            src: url("{{ public_path('fonts/External/LucidaHandwritingStdBold.TTF') }}") format('truetype');
            font-style: italic;
        }

.giac-header {
    text-align: center;
    margin-bottom: 6px;
}
.giac-logo {
    width: 145px;
    height: auto;
    margin-bottom: 2px;
}
.giac-chapeau {
    font-size: 11pt;
    font-style: italic;
    margin-top: 2px;
    margin-bottom: 10px;
    text-align: center;
}

.box-title {
    border: 1pt solid #000000;
    padding: 8px 14px;
    text-align: center;
    background-color: #F6D5DC;
    width: 55%;
    margin: 0 auto 20px auto;
}
.box-title h2 {
    margin: 0;
    font-size: 11pt;
    font-weight: bold;
    font-style: italic;
    line-height: 1.3;
}

.info-table {
    width: 65%;
    margin: 0 auto 16px auto;
    border-collapse: collapse;
}
.info-table td {
    border: 1pt solid #000000;
    padding: 12px 10px;
    font-size: 10.5pt;
    vertical-align: middle;
}

.doc-body {
    margin-left: 40px;
    margin-right: 30px;
    font-size: 10.5pt;
    line-height: 1.5;
    text-align: left;
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
            margin: 16px 15px;
        }

        .document-title-box .subtitle {
            display: block;
            font-size: 18px;
        }

/*.footer-wrapper {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
}
.pink-divider {
    border-top: 4px solid #40181c;
    border-bottom: 1px solid #40181c;
    height: 0;
    margin-bottom: 3px;
    padding-top: 1px;
}
.footer-content {
    text-align: center;
    font-size: 8.5pt;
    color: #000000;
    line-height: 1.25;
 }
.footer-content strong {
    color: #000000;
    font-style: italic;
    font-weight: bold;
    font-size: 8.5pt;
}
.footer-content a {
    color: #2b4c7e;
    text-decoration: underline;
}*/
    </style>
</head>
<body>

<div class="giac-header-logo">
    @php
        $logoPath = public_path('images/giac/logo-giac.png');
    @endphp

    @if(file_exists($logoPath))
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}">
    @else
        <h1 style="color: #c00000; font-size: 22px; margin: 0;">GIAC Technologies</h1>
    @endif
    <div class="giac-header-rule"></div>
</div>
<div class="giac-header-text">Groupement Interprofessionnel d'Aide au Conseil</div>

<div class="document-title-box">
    B -1) <span style="text-decoration: underline;">Bulletin d'Adhésion</span>
    <span class="caption">(Pour une nouvelle Entreprise)</span>
</div>

<div class="field-line" style="text-align: center;">Par la présente :</div>

<table class="info-box">
    <tr>
        <td>
            <span class="field-label">L'Entreprise</span> :
            <span>{{ $entreprise->raison_sociale ?? '.........................................................................' }}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field-label">Adresse</span> :
            <span>{{ $entreprise->siege_social ?? '.........................................................................' }}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field-label">Tel.</span> :
            <span>{{ $entreprise->telephone ?? '......................' }}</span>
            &nbsp;&nbsp;&nbsp;
            <span class="field-label" style="margin-left: 40px;">Fax</span> :
            <span>{{ $entreprise->fax ?? '......................' }} </span>
        </td>
    </tr>
</table>

<div class="field-line" style="margin-top: 12px;">
    Demande son <strong>Adhésion</strong> au <strong>GIAC Technologies,</strong>
</div>
<div class="field-line">
    pour l'année <strong>{{ $entreprise->anneesFormations()[0] ?? ($annee ?? '....................') }},</strong>
</div>

{{--<div class="footer-wrapper">
    <div class="pink-divider"></div>
    <div class="field-line">
        <span class="handwritten-text">GIAC Technologies</span>
        <span class="footer-line2">- 2 Rue Abou Said Assoussi, Résidence El Fariss, 1<sup>er</sup> étage, Appartement n° 9, Casablanca</span>
    </div>
    <div class="field-line footer-line2">
        Tél. : 0522 27 24 93 &ndash; Fax : 0522 27 57 65 &ndash; CNSS : 7365514 &ndash; e-mail :
        <a href="mailto:giactechnologies@gmail.com">giactechnologies@gmail.com</a>
        &ndash; web : <a href="http://www.giactechnologies.com">www.giactechnologies.com</a>
    </div>
</div>--}}

<x-giac-footer />

</body>
</html>
