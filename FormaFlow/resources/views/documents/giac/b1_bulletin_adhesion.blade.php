<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>B-1 Bulletin d'Adhésion</title>
    <style>
     @page {
    margin: 15px 30px 45px 30px;   
}

body {
    font-family: 'DejaVu Serif', 'Times New Roman', Times, serif;
    font-size: 11pt;
    color: #000000;
    line-height: 1.4;
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

.doc-body p {
    margin-bottom: 8px;
    margin-top: 0;
    text-align: left;
}

.intro-presente {
    text-align: center;
    font-style: italic;
    margin-bottom: 12px;
}

.list-items {
    margin-left: 45px;
    margin-top: 3px;
    margin-bottom: 10px;
    text-align: left;
}
.list-items div {
    margin-bottom: 3px;
}

.footer-wrapper {
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
}
    </style>
</head>
<body>

<div class="giac-header">
    @php
        $logoPath = public_path('images/Logo.png');
    @endphp

    @if(file_exists($logoPath))
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="giac-logo">
    @else
        <h1 style="color: #c00000; font-size: 22px; margin: 0;">GIAC Technologies</h1>
    @endif

    <div class="giac-chapeau">Groupement  Interprofessionnel  d'Aide  au  Conseil</div>
</div>

<div class="box-title">
    <h2><i><strong>B -1)   <u>Bulletin   d'Adhésion</u></strong><br><span style="font-size: 11pt;">(Pour une nouvelle Entreprise)</span></i></h2>
</div>

<p class="intro-presente">Par   la   présente :</p>

<table class="info-table">
    <tr>
        <td colspan="2">
            <i><u><strong>L'Entreprise :</strong></u></i> &nbsp; {{ $entreprise->raison_sociale ?? '.........................................................................' }}
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <i><u><strong>Adresse :</strong></u></i> &nbsp; {{ $entreprise->siege_social ?? '.........................................................................' }}
        </td>
    </tr>
    <tr>
        <td style="width: 50%; border-right: none;">
            <i><u><strong>Tel. :</strong></u></i> &nbsp; {{ $entreprise->telephone ?? '......................' }}
        </td>
        <td style="width: 50%; border-left: none;">
            <i><u><strong>Fax :</strong></u></i> &nbsp; {{ $entreprise->fax ?? '......................' }}
        </td>
    </tr>
</table>

<div class="doc-body">
    <p style="text-align: center;">
        <i>Demande   son   <strong>Adhésion</strong>   au   <strong>GIAC Technologies</strong>  pour   l'année  <strong>{{ $entreprise->anneesFormations()[0] ?? ($annee ?? '....................') }}</strong></i>
    </p>

    <p style="margin-bottom: 4px;"><i>Les  Frais  de  Cotisation  &  de  Traitement  de  dossier :</i></p>

    <div class="list-items">
        <div>• <i>La  Cotisation  Annuelle :    <strong>500,00  DHs</strong>  (Cinq Cents  Dirhams),</i></div>
        <div>• <i><u>Traitement  de  Dossier :</u> <strong>3.000,00  Dhs</strong>  (pour chaque  étude  D.S.  ou  I.F.),</i></div>
    </div>

    <p style="margin-top: 10px; margin-bottom: 6px;">
        <i>sont  réglés  par  le  <strong>Chèque  ci-joint   :</strong></i>
    </p>

    <div class="list-items" style="margin-bottom: 16px;">
        <div>• <i>Sur   la   Banque :</i> .........................................................................</div>
        <div>• <i>De   N° :</i> ..........................................................................................</div>
        <div>• <i>Daté    du :</i> ......................................................................................</div>
    </div>

    <p style="margin-bottom: 14px;">
        - <i><strong>Lieu    &        Date   (de  cette   demande)   :</strong></i> Fait à {{ $entreprise->ville ?? '....................' }}, le {{ ($dateEdition ?? now())->format('d/m/Y') }}
    </p>

    <p style="margin-bottom: 22px;">- <i><strong>Signature   :</strong></i></p>

    <p style="margin-bottom: 2px;">
        - <i><strong>Nom   et   Qualité    du    Signataire :</strong></i> {{ $gerant->prenom ?? '' }} {{ $gerant->nom ?? '' }} {{ !empty($gerant->fonction) ? '— ' . $gerant->fonction : '................................................................' }}
    </p>

    <p style="margin-left: 15px; margin-bottom: 16px; margin-top: 0;">
        <i>( Habilité à signer au sein de l'Entreprise )</i>
    </p>

    <p style="margin-bottom: 22px;">
        - <i><strong>Mail ( du signataire ) :</strong></i> {{ $gerant->email ?? '..................................................' }}
    </p>

    <p style="margin-bottom: 35px;">- <i><strong>Cachet     de    l'Entreprise :</strong></i></p>
</div>

<div class="footer-wrapper">
    <div class="pink-divider"></div>
    <div class="footer-content">
        <strong>GIAC Technologies</strong> - 2 Rue Abou Said Assoussi, Résidence El Fariss, 1<sup>er</sup> étage, Appartement n° 9 — Casablanca<br>
        Tél : 0522 27 24 93 – Fax : 0522 27 57 65 – CNSS : 7365514 – e-mail : <a href="mailto:giactechnologies@gmail.com">giactechnologies@gmail.com</a> – web : <a href="http://www.giactechnologies.com" target="_blank">www.giactechnologies.com</a>
    </div>
</div>

</body>
</html>