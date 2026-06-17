<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order</title>

    <style>
        body {
            font-family: serif;
            font-size: 12px;
        }

        .container {
            width: 100%;
        }

        .title {
            text-align: center;
            margin-top: 20px;
        }

        .title h3 {
            margin: 0;
        }

        .info {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        /* 🔥 DEFAULT (UNTUK TABEL UTAMA ADA GARIS) */
        .main-table, .main-table th, .main-table td {
            border: 1px solid #ccc;
        }

        th {
            background: #b30000;
            color: white;
            text-align: center;
            padding: 6px;
        }

        td {
            padding: 6px;
        }

        .text-right {
            text-align: right;
        }

        /* 🔥 HEADER TANPA GARIS */
        .no-border, .no-border td, .no-border tr {
            border: none !important;
        }

        /* 🔥 TABEL TTD TANPA GARIS */
        .ttd-table td {
            border: none !important;
            padding: 2px 0;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
        }

        .ttd {
            float: right;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <table class="no-border" style="margin-bottom:20px;">
        <tr>
            <td style="width:50%; text-align:left;">
                <img src="{{ public_path('asset/image/Logo_aston.png') }}" style="height:70px;">
            </td>
    
            <td style="width:50%; text-align:right;">
                <div style="background:#8b0000; color:white; padding:10px; font-size:12px; display:inline-block;">
                    Jl. Raya Bomang, Desa Jampang
                    RT 001 / RW 005, Kec. Kemang
                    Kabupaten Bogor, Jawa Barat 16310.<br>
                    Email: marketing@astonindonesia@gmail.com<br>
                    No.Telp : 0851-2296-3317
                </div>
            </td>
        </tr>
    </table>

    <!-- TUJUAN -->
    <div class="info">
        <p>
            Kepada : {{ $po->supplier->name_pt ?? '-' }} <br>
            Up : {{ $po->supplier->name ?? '-' }}
        </p>
    </div>

    <!-- TITLE -->
    <div class="title">
        <h3><u>Purchase Order (PO)</u></h3>
        <p>No. {{ $po->no_po }}</p>
    </div>

    <!-- CONTENT -->
    <div class="info">
        <p>Dengan Hormat,</p>

        <p>Yang bertanda tangan dibawah ini :</p>

        <!-- 🔥 TTD TANPA GARIS -->
        <table class="ttd-table">
            <tr>
                <td style="width:120px;">Nama</td>
                <td style="width:10px;">:</td>
                <td>Asep As'ary, S.Sos.</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>Direktur Utama</td>
            </tr>
            <tr>
                <td>Perusahaan</td>
                <td>:</td>
                <td>PT. Istimewa Aston Indonesia</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>Jl. Raya Bomang, Kemang</td>
            </tr>
        </table>

        <p>
            @if($po->details->count() > 1)
                Dengan ini memberikan Purchase Order (PO) untuk :
            @else
                Dengan ini memberikan Purchase Order (PO) untuk {{ $po->details->first()->inventory->name_material ?? '-' }} dengan jumlah sebagai berikut :
            @endif
        </p>
    </div>

    <!-- 🔥 TABEL UTAMA (ADA GARIS) -->
    <table class="main-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Uraian</th>
                <th>Satuan</th>
                <th>Volume</th>
                <th>Harga Satuan</th>
                <th>Total Harga</th>
            </tr>
        </thead>

        <tbody>
            @foreach($po->details as $i => $d)
            <tr>
                <td align="center">{{ $i+1 }}</td>
                <td>{{ $d->inventory->name_material ?? '-' }}</td>

                <td align="center">
                    @if(strtolower($d->unit) === 'kg' && $d->qty >= 1000)
                        Ton
                    @else
                        {{ ucfirst($d->unit ?? 'Kg') }}
                    @endif
                </td>
                
                <td align="center">
                    @if(strtolower($d->unit) === 'kg' && $d->qty >= 1000)
                        {{ rtrim(rtrim(number_format($d->qty / 1000, 2, ',', '.'), '0'), ',') }}
                        <div style="font-size:10px; color:#777;">
                            ({{ number_format($d->qty,0,',','.') }} Kg)
                        </div>
                    @else
                        {{ number_format($d->qty,0,',','.') }}
                    @endif
                </td>

                <td align="center">Rp {{ number_format($d->price,0,',','.') }}</td>
                <td align="center">Rp {{ number_format($d->total,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top:10px;">* Harga termasuk PPN</p>

    <!-- TTD -->
    <!-- TTD + FOOTER JADI SATU -->
<table class="no-border" style="margin-top:40px; width:100%;">
    <tr>

        <!-- KIRI -->
        <td style="width:50%; vertical-align:bottom; text-align:left; font-weight:bold;">
            PT. ISTIMEWA ASTON INDONESIA
        </td>

        <!-- KANAN -->
        <td style="width:50%; text-align:right;">

            Ditetapkan di : Bogor<br>
            Tanggal : {{ date('d M Y', strtotime($po->tanggal)) }}<br><br>

            <!-- QR -->
            <div style="margin-bottom:8px;">
                <img src="{{ public_path('asset/image/QR_dirut.jpeg') }}" style="height:70px;">
            </div>

            <!-- NAMA -->
            <div style="margin-bottom:10px;">
                <strong>ASEP AS'ARY, S.Sos</strong>
            </div>

            <!-- BOX MERAH -->
            <div style="
                background:#8b0000;
                color:white;
                padding:10px 20px;
                display:inline-block;
                font-weight:bold;
            ">

                <!-- BARIS 1 -->
                <div style="display:flex;">
                    <div style="width:100px;">ISTIMEWA</div>
                    <div>PRODUKNYA</div>
                </div>

                <!-- BARIS 2 -->
                <div style="padding-left:100px;">PELAYANANNYA</div>

                <!-- BARIS 3 -->
                <div style="padding-left:100px;">HARGANYA</div>

            </div>

        </td>

    </tr>
</table>

</div>

</body>
</html>