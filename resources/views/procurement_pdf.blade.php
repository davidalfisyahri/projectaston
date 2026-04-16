<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .info {
            width: 100%;
            margin-bottom: 15px;
        }

        .info td {
            vertical-align: top;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th, .table td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }

        .table th {
            background: #f3f3f3;
        }

        .text-left {
            text-align: left;
        }

        .signature {
            margin-top: 40px;
            width: 100%;
        }

        .signature td {
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <h2>PURCHASE ORDER</h2>
    </div>

    <!-- INFO -->
    <table class="info">
        <tr>
            <!-- LOGO -->
            <td width="15%">
                <img src="{{ public_path('asset/image/Logo_aston.png') }}" width="80">
            </td>
    
            <!-- INFO PT -->
            <td width="45%">
                <strong>PT ISTIMEWA ASTON INDONESIA</strong><br>
                Jl. Bojong Koneng, Cibinong<br>
                0812-xxxx-xxxx
            </td>
    
            <!-- NO PO -->
            <td width="40%">
                <strong>No:</strong> {{ $po->no_po }} <br>
                <strong>Tanggal:</strong> {{ $po->tanggal }}
            </td>
        </tr>
    </table>

    <!-- KEPADA -->
    <p><strong>Kepada Yth:</strong><br>
        <strong>{{ $po->supplier->name_pt ?? '-' }}</strong><br>
        {{ $po->supplier->name ?? '-' }}<br>
        {{ $po->supplier->address ?? '-' }}
    </p>

    <!-- TABLE -->
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th class="text-left">Jenis Barang</th>
                <th>Satuan</th>
                <th>Volume</th>
                <th>Harga Satuan</th>
                <th>Jumlah Harga</th>
            </tr>
        </thead>

        <tbody>
            @foreach($po->details as $i => $d)
            <tr>
                <td>{{ $i+1 }}</td>
                <td class="text-left">{{ $d->inventory->name_material ?? '-' }}</td>
                <td>{{ $d->unit }}</td>
                <td>{{ rtrim(rtrim(number_format($d->qty,2,',','.'),'0'),',') }}</td>
                <td>{{ number_format($d->price,0,',','.') }}</td>
                <td>{{ number_format($d->total,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTAL -->
    <table width="100%" style="margin-top:10px;">
        <tr>
            <td width="70%"></td>
            <td>
                <strong>Total: {{ number_format($po->total,0,',','.') }}</strong>
            </td>
        </tr>
    </table>

    <!-- SIGNATURE -->
    <table class="signature">
        <tr>
            <td width="50%">
                Hormat Kami,<br><br><br><br>
                <strong>{{ $po->created_by }}</strong>
            </td>

            <td width="50%">
                Mengetahui,<br><br><br><br>
                <strong>Direktur</strong>
            </td>
        </tr>
    </table>

</body>
</html>