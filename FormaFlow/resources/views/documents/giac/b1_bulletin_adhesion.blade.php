@extends('documents.layouts.base')

@section('content')
    <div style="text-align: center; margin-top: 50px;">
        <h1>GIAC - B1 : Bulletin d'Adhésion</h1>
        <p><strong>Entreprise :</strong> {{ $entreprise->raison_sociale ?? 'N/A' }}</p>
        <p>Document Placeholder GIAC - B1</p>
    </div>
@endsection