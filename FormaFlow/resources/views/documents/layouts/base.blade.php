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
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
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
            border-bottom: 1px solid #ccc;
            font-size: 10px;
            color: #555;
        }

        header .organisme-nom {
            font-size: 12px;
            font-weight: bold;
            color: #1a1a1a;
        }

        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 40px;
            border-top: 1px solid #ccc;
            padding-top: 6px;
            font-size: 9px;
            color: #777;
            text-align: center;
        }

        .document-subtitle {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin-top: 76px;
        }

        .document-title {
            text-align: center;
            font-size: 15px;
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
    {{-- Allows a child document (e.g. modele6) to add its own page-specific CSS
         (title casing, list style, etc.) without touching this shared layout --}}
    @stack('styles')
</head>
<body>
<header>
    <div class="organisme-nom">{{ $organisme->raison_sociale ?? config('app.name') }}</div>
    @if(!empty($organisme->siege_social ?? null))
        {{ $organisme->siege_social }}
    @endif
    @if(!empty($organisme->ice ?? null))
        — ICE {{ $organisme->ice }}
    @endif
</header>

<footer>
    Document généré automatiquement le {{ ($dateEdition ?? now())->format('d/m/Y') }} — FormaFlow
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
