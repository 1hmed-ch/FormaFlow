<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('documentTitle', 'Document')</title>
    <style>
        @page {
            margin: 100px 60px 70px 60px;
        }

        body {
            font-family: 'Times New Roman',Times, serif;
            font-size: 17px;
            color: #1a1a1a;
            line-height: 1.5;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 60px;
            padding-bottom: 8px;
            /*border-bottom: 1px solid #ccc;*/
            font-size: 10px;
            color: #555;
            text-align: center;
        }

        header .organisme-nom {
            font-size: 12px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .entete-image {
            max-height: 150px;
            max-width: 100%;
        }

        .pied-page-image {
            max-height: 80px;
            max-width: 100%;
        }

        footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            /*border-top: 1px solid #ccc;*/
            padding-top: 6px;
            font-size: 9px;
            color: #777;
            text-align: center;
        }

        .document-subtitle {
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            margin-top: 76px;
        }

        .document-title {

            text-align: center;
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 24px;
            letter-spacing: 0.5px;
        }

        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        table.info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        table.themes-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }

        table.themes-table th,
        table.themes-table td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }

        table.themes-table th {
            background-color: #f2f2f2;
        }

        .signature-block {
            margin-top: 60px;
            width: 260px;
            float: right;
            text-align: center;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 4px;
        }

        .clearfix::after {
            content: "";
            display: block;
            clear: both;
        }
    </style>

    @stack('styles')
</head>
<body>
<header>
    @if(!empty($enteteImage ?? null))
        <img src="{{ $enteteImage }}" class="entete-image">
    @else
        <div class="organisme-nom">{{ $organisme->raison_sociale ?? config('app.name') }}</div>
        @if(!empty($organisme->siege_social ?? null))
            {{ $organisme->siege_social }}
        @endif
        @if(!empty($organisme->ice ?? null))
            — ICE {{ $organisme->ice }}
        @endif
    @endif
</header>

<footer>
    @if(!empty($piedPageImage ?? null))
        <img src="{{ $piedPageImage }}" class="pied-page-image">
    @else
        Document généré automatiquement le {{ ($dateEdition ?? now())->format('d/m/Y') }} — Plénitude Groupe
    @endif
</footer>

<div class="document-title-block">
    @hasSection('documentSubtitle')
        <div class="document-subtitle">@yield('documentSubtitle')</div>
    @endif
    <div class="document-title">@yield('documentTitle')</div>
</div>

@yield('content')
</body>
</html>
