<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { max-height: 60px; }
        .header h1 { font-size: 16px; margin: 4px 0; }
        .header h2 { font-size: 14px; color: #444; margin: 0; }
        h3 { font-size: 13px; text-decoration: underline; margin-top: 20px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 5px 8px; vertical-align: top; }
        td.label { font-weight: bold; width: 180px; }
       td.value { padding: 5px 8px; vertical-align: top; }
       td.value.empty { border-bottom: 1px dotted #999; min-width: 200px; }
    </style>
</head>
<body>
    <div class="header">
        @if ($organisme->logo)
            <img src="{{ $organisme->logo }}" alt="Logo">
        @else
            <h1>PLENITUDE EDUCATION</h1>
        @endif
        <h2>Fiche Info Entreprise Client — {{ $entreprise->raison_sociale }}</h2>
    </div>

    <h3>Code accès</h3>
    <table>
        <tr>
            <td class="label">1. GMAIL — Login</td>
            <td class="value {{ $entreprise->gmail_login_ofppt ? '' : 'empty' }}">{{ $entreprise->gmail_login_ofppt ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">GMAIL — MDP</td>
            <td class="value {{ $entreprise->gmail_ofppt_mdp ? '' : 'empty' }}">{{ $entreprise->gmail_ofppt_mdp ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">2. OFPPT — ICE</td>
            <td class="value {{ $entreprise->ice ? '' : 'empty' }}">{{ $entreprise->ice ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">OFPPT — MDP</td>
            <td class="value {{ $entreprise->ofppt_mdp ? '' : 'empty' }}">{{ $entreprise->ofppt_mdp ?? '' }}</td>
        </tr>
    </table>

    <h3>Identifiants</h3>
    <table>
        <tr>
            <td class="label">ICE</td>
            <td class="value {{ $entreprise->ice ? '' : 'empty' }}">{{ $entreprise->ice ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Date création</td>
            <td class="value {{ $entreprise->date_creation ? '' : 'empty' }}">{{ $entreprise->date_creation?->format('d/m/Y') ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Num CNSS</td>
            <td class="value {{ $entreprise->num_cnss ? '' : 'empty' }}">{{ $entreprise->num_cnss ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">IF</td>
            <td class="value {{ $entreprise->if ? '' : 'empty' }}">{{ $entreprise->if ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">PATENTE</td>
            <td class="value {{ $entreprise->patente ? '' : 'empty' }}">{{ $entreprise->patente ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">RC</td>
            <td class="value {{ $entreprise->rc ? '' : 'empty' }}">{{ $entreprise->rc ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Siège</td>
            <td class="value {{ $entreprise->siege_social ? '' : 'empty' }}">{{ $entreprise->siege_social ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Nombre de cadres</td>
            <td class="value {{ $entreprise->effectif_cadre ? '' : 'empty' }}">{{ $entreprise->effectif_cadre ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Nombres employés</td>
            <td class="value {{ $entreprise->effectif_total ? '' : 'empty' }}">{{ $entreprise->effectif_total ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">GERANTE</td>
            <td class="value">{{ $gerant->nom }} {{ $gerant->prenom }}</td>
        </tr>
        <tr>
            <td class="label">CIN</td>
            <td class="value {{ $gerant->cin ? '' : 'empty' }}">{{ $gerant->cin ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Adresse gérant</td>
            <td class="value {{ $gerant->email ? '' : 'empty' }}">{{ $gerant->email ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Tél.</td>
            <td class="value {{ $gerant->telephone ? '' : 'empty' }}">{{ $gerant->telephone ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Mail entreprise</td>
            <td class="value {{ $entreprise->email ? '' : 'empty' }}">{{ $entreprise->email ?? '' }}</td>
        </tr>
    </table>
</body>
</html>