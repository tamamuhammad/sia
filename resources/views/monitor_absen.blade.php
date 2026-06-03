<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Monitor Absensi Fingerspot</title>
    <meta http-equiv="refresh" content="3">
    <style>
        body {
            background-color: #1e1e1e;
            color: #00ff00;
            font-family: 'Courier New', Courier, monospace;
            padding: 20px;
        }
        h2 {
            color: #ffffff;
            border-bottom: 1px solid #555;
            padding-bottom: 10px;
        }
        pre {
            background-color: #000000;
            padding: 15px;
            border: 1px solid #333;
            border-radius: 5px;
            overflow-x: auto;
            white-space: pre-wrap; /* Agar teks panjang tidak melebar keluar layar */
        }
        .btn-clear {
            background-color: #ff4c4c;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>📡 Live Data dari Mesin Revo W-202BNC</h2>
    
    <div style="margin-bottom: 20px;">
        <a href="/monitor-absen/clear" class="btn-clear">🗑️ Bersihkan Layar</a>
        <span style="color: #aaa; margin-left: 15px;">(Otomatis refresh tiap 3 detik...)</span>
    </div>

    <pre>{{ $logData }}</pre>

</body>
</html>