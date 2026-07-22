<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('documentTitle', 'Document GIAC')</title>
    <style>
        @page {
            margin: 40px 45px 40px 45px;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            font-style: italic;
            color: #1a1a1a;
            line-height: 1.45;
        }

        b, strong, .g-box-title, .document-title {
            font-style: italic;
        }

        .giac-header {
            width: 100%;
            margin-bottom: 10px;
        }

        .giac-logo {
            height: 55px;
        }

        .giac-header-text {
            font-size: 11px;
            margin-top: 2px;
        }

        .document-title-box {
            border: 1.5px solid #000;
            padding: 8px 10px;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .document-title-box .subtitle {
            display: block;
            font-size: 13px;
        }

        .field-line {
            margin: 5px 0;
        }

        .field-label {
            font-weight: bold;
        }

        .dotted-fill {
            border-bottom: 1px dotted #333;
            display: inline-block;
            min-width: 140px;
            padding: 0 4px;
        }

        .dotted-fill.wide {
            min-width: 100%;
            width: 100%;
        }

        .g-box {
            border: 1px solid #000;
            padding: 8px 10px;
            margin-bottom: 10px;
        }

        .g-box-title {
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #000;
            margin: -8px -10px 8px -10px;
            padding: 5px 10px;
            background-color: #f2f2f2;
        }

        .checkbox {
            display: inline-block;
            width: 9px;
            height: 9px;
            border: 1px solid #000;
            margin-right: 4px;
            vertical-align: middle;
            text-align: center;
            line-height: 9px;
            font-size: 9px;
        }

        .radio {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            border-radius: 50%;
            margin-right: 4px;
            vertical-align: middle;
            text-align: center;
            line-height: 8px;
            font-size: 8px;
        }

        table.effectif-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        table.effectif-table th,
        table.effectif-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-style: italic;
        }

        table.effectif-table th {
            background-color: #f2f2f2;
            text-align: center;
        }

        table.effectif-table td:nth-child(2),
        table.effectif-table td:nth-child(3) {
            text-align: center;
            width: 90px;
        }

        .signature-zone {
            margin-top: 30px;
        }

        .signature-zone .field-line {
            margin: 8px 0;
        }

        @stack('styles')
    </style>
</head>
<body>

<table class="giac-header">
    <tr>
        <td style="width: 90px; vertical-align: top;">
            <img src="{{ $giacLogo }}" class="giac-logo">
        </td>
        <td style="vertical-align: top; padding-left: 10px;">
            <div class="giac-header-text">Groupement Interprofessionnel d'Aide au Conseil</div>
        </td>
    </tr>
</table>

<div class="document-title-box">
    @yield('documentTitle')
    @hasSection('documentSubtitle')
        <span class="subtitle">@yield('documentSubtitle')</span>
    @endif
</div>

@yield('content')

</body>
</html>
