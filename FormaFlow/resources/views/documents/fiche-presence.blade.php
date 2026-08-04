<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste de présence - {{ $groupe->libelle }}</title>
    <style>
        @page {
            margin: 40px 45px;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #000;
        }

        .title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 26px;
            margin-top: 170px;
        }

        .entreprise-entete {
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .entreprise-entete img {
            max-height: 100px;
            max-width: 100%;
        }

        .entreprise-pied-page {
            text-align: center;
            margin-top: 300px;
        }

        .entreprise-pied-page img {
            max-height: 60px;
            max-width: 100%;
        }

        .info-block p {
            margin: 4px 0;
        }

        .info-block .info-label {
            font-weight: bold;
        }

        table.presence-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        table.presence-table th,
        table.presence-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 13px;
        }

        table.presence-table th {
            text-align: center;
            font-weight: bold;
        }

        table.presence-table td.csp-mark {
            text-align: center;
            width: 6%;
        }

        table.presence-table td.emargement-cell {
            width: 22%;
            height: 26px;
        }

        .footnote {
            margin-top: 10px;
            font-size: 10px;
        }

        .footnote p {
            margin: 2px 0;
        }

        .signatures {
            width: 100%;
            margin-top: 70px;
        }

        .signatures td {
            width: 50%;
            vertical-align: top;
            font-weight: bold;
        }
        .signatures td.signature-right {
            padding-left: 120px;
        }
    </style>
</head>
<body>

@if(!empty($enteteImage ?? null))
    <div class="entreprise-entete">
        <img src="{{ $enteteImage }}">
    </div>
@endif

<div class="title">Liste de présence par action et par groupe</div>

<div class="info-block">
    <p><span class="info-label">Entreprise :</span> {{ $entreprise->raison_sociale }}</p>
    <p><span class="info-label">Thème de l'action :</span> {{ $theme->intitule }}</p>
    <p>
        <span class="info-label">Jours de réalisation :</span>
        Du {{ $theme?->date_debut?->format('d/m/Y') ?? '--' }} au {{ $theme?->date_fin?->format('d/m/Y') ?? '--' }}
    </p>
</div>

<table class="presence-table">
    <thead>
    <tr>
        <th rowspan="2">Prénom</th>
        <th rowspan="2">Nom</th>
        <th rowspan="2">N° CIN</th>
        <th rowspan="2">N° CNSS</th>
        <th colspan="3">C.S.P*</th>
        <th rowspan="2">Emargement</th>
    </tr>
    <tr>
        <th>C</th>
        <th>E</th>
        <th>O</th>
    </tr>
    </thead>
    <tbody>
    @foreach($participants as $participant)
        @php $csp = $participant->categorie_sp?->value; @endphp
        <tr>
            <td>{{ $participant->prenom }}</td>
            <td>{{ $participant->nom }}</td>
            <td>{{ $participant->cin }}</td>
            <td>{{ $participant->numero_cnss ?? '' }}</td>
            <td class="csp-mark">{{ $csp === 'C' ? '*' : '' }}</td>
            <td class="csp-mark">{{ $csp === 'E' ? '*' : '' }}</td>
            <td class="csp-mark">{{ $csp === 'O' ? '*' : '' }}</td>
            <td class="emargement-cell"></td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="footnote">
    <p>(*) C.S.P : Catégorie Socio-Professionnelle</p>
    <p>C : Cadre- E : Employé- O : Ouvrier</p>
</div>

<table class="signatures">
    <tr>
        <td>Cachet de l'organisme de formation<br>Et identité du signataire</td>
        <td class="signature-right">Cachet et signature du responsable<br>de formation de l'entreprise</td>
    </tr>
</table>

@if(!empty($piedPageImage ?? null))
    <div class="entreprise-pied-page">
        <img src="{{ $piedPageImage }}">
    </div>
@endif

</body>
</html>
