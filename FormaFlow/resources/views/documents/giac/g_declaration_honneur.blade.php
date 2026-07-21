@extends('documents.layouts.base')

@section('content')
    <div style="text-align: center; margin-top: 50px;">
        <h1>GIAC - G : Déclaration sur l'Honneur</h1>
        <p><strong>Entreprise :</strong> {{ $entreprise->raison_sociale ?? 'N/A' }}</p>
        <p>Document Placeholder GIAC - G</p>
    </div>
@endsection