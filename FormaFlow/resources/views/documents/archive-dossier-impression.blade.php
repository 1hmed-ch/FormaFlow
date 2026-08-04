<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin-top: 24px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { text-align: left; padding: 6px; border-bottom: 1px solid #eee; }
        th { background-color: #f5f5f5; }
        .badge { padding: 2px 8px; border-radius: 4px; font-size: 11px; }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .header-meta { color: #666; font-size: 11px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <h1>Dossier Archivé — {{ $entreprise->raison_sociale }}</h1>
    <div class="header-meta">
        Édité le {{ $dateEdition->format('d/m/Y à H:i') }} — Statut du dossier :
        {{ $dossier->statut?->getLabel() ?? '—' }} — Progression : {{ $progression }}%
    </div>

    <h2>Informations générales</h2>
    <table>
        <tr><td><strong>ICE</strong></td><td>{{ $entreprise->ice ?? '—' }}</td></tr>
        <tr><td><strong>RC</strong></td><td>{{ $entreprise->rc ?? '—' }}</td></tr>
        <tr><td><strong>Gérant</strong></td><td>{{ $entreprise->gerant?->nom }} {{ $entreprise->gerant?->prenom }}</td></tr>
        <tr><td><strong>Ville</strong></td><td>{{ $entreprise->ville ?? '—' }}</td></tr>
    </table>

    <h2>Documents à joindre (pièces de l'entreprise)</h2>
    <table>
        <thead>
            <tr><th>Pièce</th><th>Statut</th></tr>
        </thead>
        <tbody>
            @foreach (\App\Models\EntrepriseCliente::PIECES_JOINTES as $key => $label)
                @php $depose = $entreprise->hasMedia($key); @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td>
                        <span class="badge {{ $depose ? 'badge-success' : 'badge-danger' }}">
                            {{ $depose ? 'Déposé' : 'Manquant' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Documents GIAC générés</h2>
    <table>
        <thead>
            <tr><th>Type</th><th>Statut</th><th>Généré le</th></tr>
        </thead>
        <tbody>
            @forelse ($documentsGeneres as $document)
                <tr>
                    <td>{{ $document->type_document?->getLabel() ?? $document->type_document }}</td>
                    <td>{{ $document->statut?->getLabel() ?? $document->statut }}</td>
                    <td>{{ $document->genere_le?->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Aucun document GIAC généré pour le moment.</td></tr>
            @endforelse
        </tbody>
    </table>
    <h2>Autres documents</h2>
<table>
    <thead>
        <tr><th>Intitulé</th><th>Ajouté le</th></tr>
    </thead>
    <tbody>
        @forelse ($autresDocuments as $document)
            <tr>
                <td>{{ $document->getCustomProperty('intitule') ?: $document->file_name }}</td>
                <td>{{ $document->created_at?->format('d/m/Y H:i') }}</td>
            </tr>
        @empty
            <tr><td colspan="2">Aucun document complémentaire ajouté.</td></tr>
        @endforelse
    </tbody>
</table>
<h2>Demande de Financement (OFPPT)</h2>
<div class="header-meta">
    Statut : {{ $statutFinancement?->getLabel() ?? '—' }}
</div>
<table>
    <thead>
        <tr><th>Pièce</th><th>Statut</th></tr>
    </thead>
    <tbody>
        @foreach ($piecesOfppt as $key => $label)
            @php $depose = $entreprise->hasMedia($key); @endphp
            <tr>
                <td>{{ $label }}</td>
                <td>
                    <span class="badge {{ $depose ? 'badge-success' : 'badge-danger' }}">
                        {{ $depose ? 'Déposé' : 'Manquant' }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>