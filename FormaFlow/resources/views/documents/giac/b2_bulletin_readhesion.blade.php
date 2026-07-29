<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>B-2 - Bulletin de Ré-adhésion et de Frais de dossier</title>
    <style>
        @page {
            margin: 40px 95px 90px 95px;
        }

        @font-face {
            font-family: 'Lucida Handwriting';
            src: url("{{ public_path('fonts/External/LucidaHandwritingStdBold.TTF') }}") format('truetype');
            font-style: italic;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 15px;
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

        /* Titre encadré, fond rose clair — couleur FADAE1 vérifiée dans le
           XML du docx (w:shd w:fill="FADAE1"). */
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

        .document-title-box .caption {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-top: 2px;
        }

        .field-line {
            margin: 5px 30px;
        }

        .field-label {
            font-weight: bold;
        }

        .dotted-fill {
            border-bottom: 2px dotted #333;
            display: inline-block;
            min-width: 140px;
            padding: 0 4px;
        }

        .dotted-fill.wide {
            min-width: 100%;
            width: 100%;
        }

        table.info-box {
            width: 75%;
            border-collapse: collapse;
            /*margin-top: 8px;
            margin-bottom: 10px;*/
            margin: 10px 80px;
        }

        table.info-box td {
            border: 1px solid #000;
            padding: 13px 10px;
        }

        .signature-zone {
            margin-top: 20px;
        }

        .signature-zone .field-line {
            margin: 8px 0;
        }

        /*.giac-footer {
            position: fixed;
            bottom: -70px;
            left: -90px;
            right: -90px;
            text-align: center;
            border-top: 4px double #622423;
            padding-top: 4px;
            font-family: 'Times New Roman', Times, serif;
        }

         .giac-footer .footer-brand {
            font-weight: bold;
            font-style: italic;
            font-size: 13px;
        }

        .giac-footer .handwritten-text {
            font-family: 'Lucida Handwriting', cursive;
            font-style: italic;
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
    B -2) <span style="text-decoration: underline;">Bulletin de Ré - adhésion et de Frais</span>
    <span class="subtitle"><span style="text-decoration: underline;">de dossier, d'une nouvelle demande de prise en charge</span></span>
    <span class="caption">(Pour une Entreprise déjà membre)</span>
</div>

<div class="field-line" style="text-align: center;">Par la présente :</div>

<table class="info-box">
    <tr>
        <td>
            <span class="field-label" style="text-decoration: underline;">L'Entreprise</span> :
            <span >{{ $entreprise->raison_sociale }}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field-label" style="text-decoration: underline;">Adresse</span> :
            <span >{{ $entreprise->siege_social }}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="field-label" style="text-decoration: underline;">Tel.</span> :
            <span>{{ $entreprise->telephone }}</span>
            <span style="margin-left: 10px;">;</span>
            <span class="field-label" style="text-decoration: underline; margin-left: 40px;">Fax</span> :
            <span>{{ $entreprise->fax }}</span>
        </td>
    </tr>
</table>

<div class="field-line" style="margin-top: 12px;">
    Demande le renouvellement de son <strong>Adhésion</strong> au <strong>GIAC Technologies,</strong>
</div>
<div class="field-line">
    pour l'année <strong>{{ $annee }},</strong>
</div>
<div class="field-line">
    Et dépose auprès de ce GIAC, un nouveau dossier de prise en charge<strong>.</strong>
</div>

<div class="field-line" style="margin-top: 10px;">Les Frais de Cotisation &amp; de Traitement de dossier :</div>
<div class="field-line" style="margin-left: 95px;">
    &bull; La Cotisation Annuelle : <strong>500,00 DHs</strong> (Cinq Cents Dirhams),
</div>
<div class="field-line" style="margin-left: 95px;">
    &bull; Traitement de Dossier : <strong>3.000,00 Dhs</strong> (pour chaque étude D.S. ou I.F.),
</div>

<div class="field-line" style="margin-top: 10px;">sont réglés par le <strong>Chèque</strong> ci-joint :</div>
<div class="field-line" style="margin-left: 95px;">&bull; Sur la Banque : <span class="dotted-fill" style="width: 350px">&nbsp;</span></div>
<div class="field-line" style="margin-left: 95px;">&bull; De N° : <span class="dotted-fill" style="width: 400px">&nbsp;</span></div>
<div class="field-line" style="margin-left: 95px;">&bull; Daté du : <span class="dotted-fill" style="width: 390px">&nbsp;</span></div>

<div class="signature-zone">
    <div class="field-line">
        <strong>- Lieu &amp; Date </strong>(de cette demande) :
        <span>{{ $entreprise->ville }}; le {{ $dateEdition->format('d/m/Y') }}</span>
    </div>

    <div class="field-line" style="margin-bottom: 30px;"><strong>- Signature :</strong></div>

    <div class="field-line">
        <strong>- Nom et Qualité du Signataire :</strong>
        <span>{{ $gerant->prenom }} {{ $gerant->nom }}; {{ $gerant->fonction }}</span>
    </div>
    <div class="field-line" style="font-size: 11px;">(habilité à signer au sein de l'Entreprise)</div>

    <div class="field-line"><strong>- Mail ( du signataire ) :</strong></div>

    <div class="field-line"><strong>- Cachet de l'Entreprise :</strong></div>
</div>

{{--<div class="giac-footer">
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