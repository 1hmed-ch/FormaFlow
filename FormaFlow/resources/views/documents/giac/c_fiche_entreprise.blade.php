<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>C - Fiche d'Information sur l'Entreprise</title>
    <style>
       @page {
            margin: 10px 25px 15px 25px;
        }
        @font-face {
            font-family: 'Lucida Handwriting';
            src: url("{{ public_path('fonts/External/LucidaHandwritingStdBold.TTF') }}") format('truetype');
            font-style: italic;
        }
        body {
            font-family: 'Times New Roman', Times, 'DejaVu Serif', serif;
            font-size: 10pt;              
            color: #000000;
            line-height: 1.5;     
            margin-bottom: 0px;             
        }

        .giac-header {
            text-align: center;
            margin-bottom: 2px;
        }
        .giac-logo {
            width: 140px;
            height: auto;
            margin-bottom: 2px;
        }
        .giac-header-rule {
            border: none;
            border-top: 1px solid #A3001E;
            width: 180px;
            margin: 2px auto 4px auto;
        }
        .giac-chapeau {
            font-size: 10.5pt;
            font-style: italic;
            margin-top: 1px;
            margin-bottom: 6px;
            text-align: center;
        }

        .box-title {
            border: 1px solid #000000;
            padding: 20px 0;          
            text-align: center;
            background-color:  #F6D5DC;
            width:58%;                  
            margin: 0 auto 40px auto;   
        }
        
        .box-title h2 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            font-style: italic;
        }
        .section-box {
            width: 85%;                 
            margin: 0 auto 10px auto;      
            border: 1px solid #000000;
            border-collapse: collapse;
        }

        .section-header-td {
            border-bottom: 1px solid #000000;
            padding: 3px 0;              
            text-align: center;
            font-weight: bold;
            font-style: italic;
            font-size: 10pt;             
            background-color: #ffffff;
        }

        .inner-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .inner-table td {
            padding:3px 8px;
            height:18px;                
            vertical-align: middle;
            font-size: 9.5pt;
        }
        .inner-table td.label {
            white-space: nowrap;
            overflow: hidden;
        }
        .inner-table td.sep {
            width: 3%;
            text-align: center;
            padding: 5px 0;
            font-weight: bold;
        }

        .dotted-fill {
            letter-spacing: 1px;
        }

       .giac-footer {
            position: fixed;
            bottom:18px;
            left: -25px;
            right: -25px;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
        }

.giac-footer .pink-divider {
    border-top: 4px solid #501f24;
    border-bottom: 1px solid #501f24;
    height: 0;
    margin-bottom: 3px;
    padding-top: 1px;
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
}
    </style>
</head>
<body>

<div class="giac-header">
    @php
        $logoPath = public_path('images/giac/logo-giac.png');
    @endphp

    @if(file_exists($logoPath))
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="giac-logo">
    @else
        <h1 style="color: #c00000; font-size: 18px; margin: 0;">GIAC Technologies</h1>
    @endif
    <div class="giac-header-rule"></div>
    <div class="giac-chapeau">Groupement Interprofessionnel d'Aide au Conseil</div>
</div>

<div class="box-title">
    <h2><i><strong>C) Fiche d'Information sur l'Entreprise</strong></i></h2>
</div>

<table class="section-box">
    <tr>
        <td class="section-header-td"><u>GENERALITES</u></td>
    </tr>
    <tr>
        <td style="padding: 4px 6px;">
            <table class="inner-table">
                <tr>
                    <td class="label" style="width: 20%;"><i><strong>Raison Sociale :</strong></i></td>
                    <td colspan="3">{{ !empty($entreprise->raison_sociale) ? $entreprise->raison_sociale : '..............................................' }}</td>
                </tr>
                <tr>
                    <td class="label"><i><strong>Activités Principales :</strong></i></td>
                    <td colspan="3">{{ !empty($entreprise->activite) ? $entreprise->activite : (!empty($entreprise->secteur_activite) ? $entreprise->secteur_activite : '.........................................') }}</td>
                </tr>
                <tr>
                    <td class="label"><i><strong>Date de Création :</strong></i></td>
                    <td colspan="3">{{ !empty($entreprise->date_creation) ? \Carbon\Carbon::parse($entreprise->date_creation)->format('d/m/Y') : '...................................................' }}</td>
                </tr>
                <tr>
                    <td class="label"><i><strong>Adresse :</strong></i></td>
                    <td colspan="3">{{ !empty($entreprise->siege_social) ? $entreprise->siege_social : '........................................' }}</td>
                </tr>
                <tr>
                    <td class="label" style="width: 16%;"><i><strong>Téléphone :</strong></i></td>
                    <td style="width: 44%;">{{ !empty($entreprise->telephone) ? $entreprise->telephone : '................................' }} ;</td>
                    <td class="label" style="width: 10%; text-align: right; padding-right: 5px;"><i><strong>Fax :</strong></i></td>
                    <td style="width: 30%;">{{ !empty($entreprise->fax) ? $entreprise->fax : '................................' }}</td>
                </tr>
                <tr>
                    <td class="label"><i><strong>Mail de l'Entreprise :</strong></i></td>
                    <td colspan="3">{{ !empty($entreprise->email) ? $entreprise->email : '.........................................' }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="white-space: nowrap;">
                        <i><strong>Nom et Titre de la Personne à contacter :</strong></i> &nbsp;
                        @if(!empty($entreprise->contact_ref))
                            {{ $entreprise->contact_ref }}
                        @elseif(!empty($gerant))
                            {{ $gerant->prenom }} {{ $gerant->nom }} {{ !empty($gerant->fonction) ? ' ; ' . $gerant->fonction : '' }}
                        @else
                            .......................................
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="section-box">
    <tr>
        <td class="section-header-td">
            <u>EFFECTIF de l'ENTREPRISE</u><br>
            <span style="font-size: 8.5pt; font-weight: normal; font-style: italic;">( à la date de la demande )</span>
        </td>
    </tr>
    <tr>
        <td style="padding: 4px 6px;">
            <table class="inner-table">
                <tr>
                    <td class="label" style="width: 9%;"><i><strong>Cadres :</strong></i></td>
                    <td style="width: 16%;">{{ $entreprise->effectif_cadre ?? '..........' }}</td>
                    <td class="sep">;</td>
                    <td class="label" style="width: 16%;"><i><strong>Cadre Moyens :</strong></i></td>
                    <td style="width: 16%;">{{ $entreprise->effectif_cadre_moyen ?? '..........' }}</td>
                    <td class="sep">;</td>
                    <td class="label" style="width: 16%;"><i><strong>Agents Qualifiés :</strong></i></td>
                    <td style="width: 16%;">{{ $entreprise->effectif_agent_qualifie ?? '..........' }}</td>
                </tr>
                <tr>
                    <td class="label" colspan="2"><i><strong>Agents Sans Qualification :</strong></i></td>
                    <td>{{ $entreprise->effectif_agent_sans_qualification ?? '..........' }}</td>
                    <td class="sep">;</td>
                    <td class="label" colspan="2" style="text-align: right; padding-right: 8px;"><i><strong>Agents Occasionnels :</strong></i></td>
                    <td>{{ $entreprise->effectif_agent_occasionnel ?? '..........' }}</td>
                </tr>
                <tr>
                    <td colspan="8" style="text-align: center; padding-top: 4px;">
                        <i><strong>Total Effectif : &nbsp;&nbsp;&nbsp; {{ $entreprise->effectif_total ?? '....................' }}</strong></i>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="section-box">
    <tr>
        <td style="padding: 2px 4px;">
            <table class="inner-table">
                <tr>
                    <td style="padding-bottom: 0;">
                        <i><strong>N° d'Affiliation à la C.N.S.S. :</strong></i> &nbsp; {{ $entreprise->num_cnss ?? '....................' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 0; padding-bottom: 0;">
                        <i><strong>Montant de la Taxe Versée * :</strong></i> &nbsp; 
                        {{ !empty($entreprise->montant_tfp) ? number_format($entreprise->montant_tfp, 2, ',', ' ') . ' DH' : '....................' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 7.5pt; font-style: italic; line-height: 1.1; padding-top: 0; padding-bottom: 0;">
                        * à l'année précédant l'actuelle demande de Financement.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 5px;">
                        <i><strong>N° R.C. Entreprise :</strong></i> &nbsp; {{ $entreprise->rc ?? '........................' }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
        </td>
    </tr>
</table>

<table class="section-box">
    <tr>
        <td style="padding: 3px 6px;">
            <div style="margin-bottom: 2px; font-style: italic; font-size: 8.5pt; line-height: 1.1;">
                <strong>Avant l'actuelle demande de Financement, avez-vous déjà déposé une demande similaire auprès d'un GIAC, en vue d'identifier vos besoins en Compétence :</strong> 
                &nbsp; <strong>{{ isset($entreprise->deja_depose_giac) ? ($entreprise->deja_depose_giac ? 'OUI' : 'NON') : '.........' }}</strong>
            </div>
            <div style="font-style: italic; font-size: 8.5pt; line-height: 1.1;">
                <strong>Si oui, quel GIAC :</strong> 
                <strong>{{ $entreprise->nom_ancien_giac ?? '............................' }}</strong> 
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                <strong>Date de dépôt de ce Dossier :</strong> 
                <strong>{{ !empty($entreprise->date_depot_ancien_giac) ? \Carbon\Carbon::parse($entreprise->date_depot_ancien_giac)->format('d/m/Y') : '............................' }}</strong>
            </div>
        </td>
    </tr>
</table>

<div
style="margin-left:50px;margin-top:12px;margin-bottom:8px;page-break-inside:avoid;">
    <p style="margin-bottom: 8px; font-size: 10.5pt;">
        <strong>- <i>Date :</i></strong> &nbsp; {{ \Carbon\Carbon::parse($dateEdition ?? now())->format('d/m/Y') }}
    </p>
    <p style="margin-bottom: 0; font-size: 10.5pt;">
        <strong>- <i>Cachet et Signature légalisée :  &nbsp;&nbsp;</i></strong>
    </p>
</div>

<div class="giac-footer">
    <div class="pink-divider"></div>
    <div class="field-line">
        <span class="handwritten-text">GIAC Technologies</span>
        <span class="footer-line2">- 2 Rue Abou Said Assoussi, Résidence El Fariss, 1<sup>er</sup> étage, Appartement n° 9 — Casablanca</span>
    </div>
    <div class="field-line footer-line2">
        Tél : 0522 27 24 93 – Fax : 0522 27 57 65 – CNSS : 7365514 – e-mail :
        <a href="mailto:giactechnologies@gmail.com">giactechnologies@gmail.com</a>
        – web : <a href="http://www.giactechnologies.com" target="_blank">www.giactechnologies.com</a>
    </div>
</div>
</body>
</html>