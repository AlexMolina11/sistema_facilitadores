<style>
    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 11px;
        color: #111;
        line-height: 1.35;
    }

    .cv-title {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin: 0 0 18px;
    }

    h2 {
        font-size: 13px;
        margin: 16px 0 8px;
        font-weight: bold;
    }

    .cv-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 11px;
    }

    .cv-table th,
    .cv-table td {
        border: 1px solid #333;
        padding: 6px;
        vertical-align: top;
    }

    .cv-table th {
        background: #f2f2f2;
        font-weight: bold;
    }

    .cv-table-clean th,
    .cv-table-clean td {
        border: none;
        background: transparent;
        padding: 4px 6px;
        text-align: left;
    }

    .cv-table-clean th {
        width: 190px;
    }

    .cv-signature {
        margin-top: 32px;
    }

    @page {
        margin: 35px;
    }

    .cv-fepade-document {
        width: 100%;
        font-family: Arial, sans-serif;
        font-size: 11px;
        color: #0D1B2A;
        line-height: 1.38;
    }

    .cv-fepade-hero {
        background: #0D1B2A;
        margin: -48px -48px 20px -48px;
        padding: 28px 48px 22px;
        border-bottom: 6px solid #00C896;
        color: #FFFFFF;
    }

    .cv-fepade-brand {
        color: #00C896;
        font-size: 8.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .14em;
        margin-bottom: 8px;
    }

    .cv-fepade-hero h1 {
        margin: 0;
        color: #FFFFFF;
        font-size: 28px;
        line-height: 1.05;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .035em;
    }

    .cv-fepade-hero p {
        display: inline-block;
        margin: 10px 0 0;
        padding: 3px 10px;
        border: 1px solid #00C896;
        border-radius: 12px;
        color: #FFFFFF;
        font-size: 9.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .cv-fepade-document h2 {
        margin: 18px 0 12px;
        padding: 7px 10px;
        background: #162032;
        color: #FFFFFF;
        font-size: 11.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .055em;
        border-left: 5px solid #00C896;
    }

    .cv-fepade-document .cv-table {
        border-collapse: collapse;
        margin-bottom: 14px;
    }

    .cv-fepade-document .cv-table th {
        background: #EEF2F8;
        color: #0D1B2A;
        font-weight: bold;
    }

    .cv-fepade-document .cv-table td,
    .cv-fepade-document .cv-table th {
        border: 1px solid rgba(13, 27, 42, 0.22);
        padding: 7px;
        vertical-align: top;
    }

    .cv-fepade-document .cv-table-clean {
        background: #F7F9FC;
        border-left: 4px solid #00C896;
        margin-bottom: 18px;
    }

    .cv-fepade-document .cv-table-clean th,
    .cv-fepade-document .cv-table-clean td {
        border: 0;
        background: transparent;
    }

    .cv-fepade-document .cv-table-clean th {
        color: #0D1B2A;
        font-weight: bold;
        width: 220px;
    }

    .cv-pdf-section {
        margin-bottom: 14px;
    }

    .cv-pdf-section-title,
    .cv-pdf-section > h2,
    h2 {
        page-break-after: avoid;
    }

    .cv-table thead {
        display: table-header-group;
    }

    .cv-table tr {
        page-break-inside: avoid;
    }

    .cv-keep-together {
        page-break-inside: avoid;
    }

    .cv-manual-text {
        white-space: pre-wrap;
        line-height: 1.45;
    }

</style>

<style>
    .cv-institutional-document,
    .cv-summary-document { color:#0D1B2A; font-family:DejaVu Sans, Arial, sans-serif; font-size:10px; line-height:1.4; }
    .cv-institutional-header,
    .cv-summary-header { margin:-28px -28px 18px; padding:22px 28px 16px; background:#0D1B2A; border-bottom:6px solid #00C896; color:#FFFFFF; page-break-inside:avoid; }
    .cv-institutional-brand,
    .cv-summary-brand { color:#00C896; font-size:8px; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase; }
    .cv-institutional-header h1,
    .cv-summary-header h1 { margin:6px 0 3px; color:#FFFFFF; font-size:19px; }
    .cv-institutional-header p,
    .cv-summary-header p { margin:0; color:#EEF2F8; font-size:9px; }
    .cv-institutional-process { padding:9px 11px; margin-bottom:14px; background:#F7F9FC; border:1px solid rgba(13,27,42,.10); border-left:4px solid #00C896; }
    .cv-numbered-title { padding:7px 9px; margin:15px 0 8px; background:#162032; border-left:5px solid #00C896; color:#FFFFFF; font-size:11px; page-break-after:avoid; }
    .cv-numbered-title span { display:inline-block; width:18px; height:18px; margin-right:7px; border-radius:9px; background:#00C896; color:#0D1B2A; text-align:center; line-height:18px; font-weight:bold; }
    .cv-institutional-table thead th,
    .cv-summary-section-row th { background:#162032; color:#FFFFFF; }
    .cv-institutional-table tbody tr:nth-child(even) td { background:#F7F9FC; }
    .cv-cell-reference { margin-top:5px; padding-top:4px; border-top:1px solid rgba(13,27,42,.16); color:#6B7A90; font-size:8.5px; }
    .cv-inline-tag { display:inline-block; padding:2px 6px; margin:2px 2px 2px 0; border-radius:8px; background:#EEF2F8; border:1px solid rgba(13,27,42,.10); color:#0D1B2A; font-size:8px; font-weight:bold; }
    .cv-inline-tag-muted { background:#F7F9FC; color:#6B7A90; }
    .cv-signature-block { width:100%; margin-top:30px; border-collapse:separate; border-spacing:12px 0; }
    .cv-signature-block td { width:33%; padding-top:7px; border-top:1px solid #0D1B2A; text-align:center; font-size:8px; vertical-align:top; }
    .cv-signature-authority { margin-top:38px; }
    .cv-summary-context { width:100%; margin-bottom:13px; border-collapse:separate; border-spacing:8px 0; }
    .cv-summary-context td { width:50%; padding:9px; background:#F7F9FC; border:1px solid rgba(13,27,42,.10); border-top:3px solid #00C896; vertical-align:top; }
    .cv-summary-context strong { display:block; margin-bottom:4px; color:#0D1B2A; font-size:8px; text-transform:uppercase; }
    .cv-summary-table th:first-child { width:34%; background:#EEF2F8; color:#0D1B2A; text-align:left; }
    .cv-summary-section-row th { padding:7px 9px; background:#162032 !important; color:#FFFFFF !important; text-align:left; text-transform:uppercase; letter-spacing:.4px; border-left:5px solid #00C896; }
    .cv-compact-list { margin:0; padding-left:16px; }
    .cv-compact-list li { margin-bottom:4px; }
    .cv-muted { color:#6B7A90; font-size:8.5px; }
    .cv-institutional-document a,
    .cv-summary-document a { color:#0099FF; text-decoration:none; font-weight:bold; }
</style>
