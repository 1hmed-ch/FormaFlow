<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('documentTitle', 'Document CSF')</title>
    <style>
        @page {
            margin: 35px 40px 35px 40px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
        }

        .csf-header-label {
            font-weight: bold;
            font-size: 13px;
        }

        .csf-header-title {
            font-weight: bold;
            font-size: 24px;
            text-align: center;
            margin: 2px 0 6px 0;
        }

        .csf-header-bar {
            height: 6px;
            background-color: #000;
            margin-bottom: 10px;
        }

        .document-heading {
            font-weight: normal;
            font-size: 18px;
            margin-bottom: 12px;
        }

        table.f3-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.f3-box td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }

        .f3-label {
            font-weight: normal;
        }

        .f3-value {
            min-height: 14px;
        }

        .f3-inline-label {
            font-weight: normal;
        }

        .f3-inline-value {
            margin-left: 4px;
        }

        .f3-inline-right {
            text-align: right;
        }

        .f3-inline-right .f3-inline-value {
            display: inline-block;
            min-width: 90px;
            text-align: left;
            margin-left: 8px;
        }

        table.effectif-table-f3 {
            width: 100%;
            border-collapse: collapse;
        }

        table.effectif-table-f3 th,
        table.effectif-table-f3 td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        table.effectif-table-f3 th {
            background-color: #f2f2f2;
            text-align: center;
        }

        table.effectif-table-f3 td:nth-child(2),
        table.effectif-table-f3 td:nth-child(3) {
            text-align: center;
            width: 110px;
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

        .footnote {
            font-size: 9px;
        }

        .portal-footer {
            font-size: 9px;
            margin-top: 10px;
        }

        .portal-footer a {
            color: #000;
        }

        @stack('styles')
    </style>
</head>
<body>

<div class="csf-header-label">Contrats Spéciaux de Formation</div>
<div class="csf-header-title">@yield('formNumber')</div>
<div class="csf-header-bar"></div>
<div class="document-heading">@yield('documentTitle')</div>

@yield('content')

</body>
</html>
