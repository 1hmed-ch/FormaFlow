@extends('documents.layouts.base')

@section('content')
    <div style="text-align: center; margin-top: 50px;">
        <h1>GIAC - E : Fiche Technique Étude Ingénierie de Formation</h1>
        <p><strong>Entreprise :</strong> {{ $entreprise->raison_sociale ?? 'N/A' }}</p>
        <p>Document Placeholder GIAC - E</p>
    </div>
@endsection