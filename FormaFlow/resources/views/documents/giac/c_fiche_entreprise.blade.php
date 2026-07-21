@extends('documents.layouts.base')

@section('content')
    <div style="text-align: center; margin-top: 50px;">
        <h1>GIAC - C : Fiche d'Information sur l'Entreprise</h1>
        <p><strong>Entreprise :</strong> {{ $entreprise->raison_sociale ?? 'N/A' }}</p>
        <p><strong>Effectif Total :</strong> {{ $entreprise->effectif_total ?? 0 }}</p>
        <p>Document Placeholder GIAC - C</p>
    </div>
@endsection