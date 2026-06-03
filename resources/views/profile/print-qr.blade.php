<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Absen - {{ $student->name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f3f4f6;
            padding-top:20px;
        }
        .card {
            background: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            text-align: center;
            width: 260px;
            border: 1px solid #e5e7eb;
        }
        
        .title {
            font-size: 14px;
            font-weight: 800;
            color: #1f2937;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .subtitle {
            font-size: 10px;
            color: #9ca3af;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .qr-container {
            margin: 15px 0;
            display: inline-block;
            padding: 10px;
            border: 1px solid #f3f4f6;
            border-radius: 12px;
        }
        .name {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-top: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .nis {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            margin-top: 4px;
        }
        
        /* Aturan khusus saat dicetak ke kertas */
        @media print {
            body {
                background: white;
                height: auto;
            }
            .card {
                box-shadow: none;
                border: 1px dashed #9ca3af; /* Garis putus-putus untuk batas potong kartu */
                page-break-inside: avoid;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="title">Kartu Presensi</div>
        <div class="subtitle">PENGAJIAN AL-QUR'RAN KOMPLEK L</div>
        
        <div class="qr-container">
            {!! QrCode::size(160)->margin(1)->generate($payload) !!}
        </div>
        
        <div class="name">{{ $student->name }}</div>
        <div class="nis">NIS: {{ $student->nis ?? '-' }}</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.close();
            }
        }
    </script>
</body>
</html>