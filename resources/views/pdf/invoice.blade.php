@php
if (!function_exists('terbilang')) {
    function terbilang($angka) {
        $angka = abs($angka);
        $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $terbilang = "";
        
        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } else if ($angka < 20) {
            $terbilang = terbilang($angka - 10) . " belas";
        } else if ($angka < 100) {
            $terbilang = terbilang(floor($angka / 10)) . " puluh" . terbilang($angka % 10);
        } else if ($angka < 200) {
            $terbilang = " seratus" . terbilang($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = terbilang(floor($angka / 100)) . " ratus" . terbilang($angka % 100);
        } else if ($angka < 2000) {
            $terbilang = " seribu" . terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = terbilang(floor($angka / 1000)) . " ribu" . terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $terbilang = terbilang(floor($angka / 1000000)) . " juta" . terbilang($angka % 1000000);
        } else if ($angka < 1000000000000) {
            $terbilang = terbilang(floor($angka / 1000000000)) . " milyar" . terbilang(fmod($angka, 1000000000));
        } else if ($angka < 1000000000000000) {
            $terbilang = terbilang(floor($angka / 1000000000000)) . " trilyun" . terbilang(fmod($angka, 1000000000000));
        }
        return trim($terbilang);
    }
}
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice</title>

<style>
body{
    font-family: Arial, sans-serif;
    font-size:11px;
}

table{
    width:100%;
    border-collapse:collapse;
}

.border td,
.border th{
    border:1px solid #000;
    padding:4px;
}

.text-center{
    text-align:center;
}

.text-right{
    text-align:right;
}

.header-red{
    background:#8b0015;
    color:white;
}

.title{
    font-size:22px;
    font-weight:bold;
}

.footer-note{
    font-size:10px;
    line-height:15px;
}
</style>

</head>
<body>

<table>
<tr>

<td width="35%">
    <img src="{{ public_path('asset/image/Logo_aston.png') }}"
         style="width:180px;">
</td>

<td width="65%" class="header-red" style="padding:10px;">
    <b>Jl. Raya Bomang (Bojong Gede-Kemang)</b><br>
    Desa Jampang RT 001/005 Kec. Kemang<br>
    Kab. Bogor (16310)<br>
    Email : istimewaastonindonesiapt@gmail.com<br>
    No.Telp : 085 122 96 3317
</td>

</tr>
</table>

<hr>

<div class="text-center title">
    INVOICE
</div>

<br>

<table>
<tr>
    <td width="15%">No Invoice</td>
    <td width="2%">:</td>
    <td>{{ $data->request_code }}</td>
</tr>

<tr>
    <td>Customer</td>
    <td>:</td>
    <td>{{ $data->customer_name }}</td>
</tr>

<tr>
    <td>Project</td>
    <td>:</td>
    <td>{{ $data->ongoing_project ?? '-' }}</td>
</tr>

<tr>
    <td>Tanggal Invoice</td>
    <td>:</td>
    <td>{{ $data->invoice_date ? \Carbon\Carbon::parse($data->invoice_date)->format('d/m/Y') : '-' }}</td>
</tr>
</table>

<br>

<table class="border">

<thead>

<tr>
    <th width="5%">No</th>
    <th>Keterangan</th>
    <th width="12%">Tanggal</th>
    <th width="15%">Mutu Beton</th>
    <th width="8%">QTY</th>
    <th width="12%">Price</th>
    <th width="15%">Total</th>
</tr>

</thead>

<tbody>

@php
$subtotal = 0;
@endphp

@foreach($data->details as $row)

@php
$qty = $row->qty ?? 0;
$price = $row->price ?? 0;

$total = $qty * $price;
$subtotal += $total;
@endphp

<tr>

<td class="text-center">
    {{ $loop->iteration }}
</td>

<td>
    {{ $row->description ?? '-' }}
</td>

<td>
    {{ \Carbon\Carbon::parse($data->tanggal)->format('d/m/Y') }}
</td>

<td>
    @if($row->grade)
        {{ $row->grade->name_grade }}
    @else
        -
    @endif

    @if($row->type)
        ({{ strtoupper($row->type) }})
    @endif
</td>

<td class="text-center">
    {{ number_format($qty) }} cm³
</td>

<td class="text-right">
    {{ number_format($price,0,',','.') }}
</td>

<td class="text-right">
    {{ number_format($total,0,',','.') }}
</td>

</tr>

@endforeach

<tr>
    <td colspan="3"></td>
    <td colspan="3" class="text-center">
        <b>SUBTOTAL BETON</b>
    </td>
    <td class="text-right">
        {{ number_format($subtotal,0,',','.') }}
    </td>
</tr>

@if($data->discount_amount > 0)
<tr>
    <td colspan="3"></td>
    <td colspan="3" class="text-center">
        <b>DISKON</b>
    </td>
    <td class="text-right">
        - {{ number_format($data->discount_amount,0,',','.') }}
    </td>
</tr>
@endif

@if($data->delivery_fee > 0)
<tr>
    <td colspan="3"></td>
    <td colspan="3" class="text-center">
        <b>BIAYA PENGIRIMAN ({{ $data->delivery_distance }} km)</b>
    </td>
    <td class="text-right">
        {{ number_format($data->delivery_fee,0,',','.') }}
    </td>
</tr>
@endif

@php
$grandTotal = ($subtotal - ($data->discount_amount ?? 0)) + ($data->delivery_fee ?? 0);
@endphp

<tr>
    <td colspan="3"></td>
    <td colspan="3" class="text-center">
        <b>GRAND TOTAL</b>
    </td>
    <td class="text-right">
        <b>{{ number_format($grandTotal,0,',','.') }}</b>
    </td>
</tr>

</tbody>

</table>

<br>

<table class="border">

<tr>

<td width="50%">
</td>

<td width="50%" style="vertical-align:top">

<center>

<b>The amount of ( Terbilang )</b>

<br><br>

{{ function_exists('terbilang') ? ucwords(terbilang(round($grandTotal))) : number_format($grandTotal,0,',','.') }}

Rupiah

</center>

</td>

</tr>

</table>

<br>

<table width="100%">

<tr>

    <td width="70%" class="footer-note" style="vertical-align:top;">

        <b>Terms & Conditions</b><br><br>
    
        1. All amounts written are deemed correct and accepted
        by the buyer if there is a written objection within 7 days
        after receipt of the invoice.<br><br>
    
        2. This receipt is only considered valid after payment
        by Giro/Check/Transfer. The amount can be cashed/entered
        into the PT Istimewa Aston Indonesia Account.<br><br>
    
        3. If payment has been made in accordance with the invoiced amount,
        the delivery of the concrete may proceed.
    
        <br><br>
    
        <hr style="border:0; border-top:1px solid #000;">
    
        <br>
    
        <b>Syarat & Ketentuan</b><br><br>
    
        1. Semua jumlah yang tertulis dianggap benar dan diterima
        oleh pembeli jika ada sanggahan tertulis dalam waktu 7 hari
        setelah penerimaan tagihan.<br><br>
    
        2. Kwitansi ini baru dianggap sah setelah pembayaran
        dengan Giro/Cek/Transfer tersebut dapat diuangkan atau masuk
        ke Rekening PT Istimewa Aston Indonesia.<br><br>
    
        3. Jika sudah ada pembayaran sesuai dengan nominal invoice,
        maka pengiriman beton dapat dilakukan.
    
    </td>

<td width="30%" align="center">

Kemang,
{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}

<br><br><br><br>

<img src="{{ public_path('asset/image/QR_dirut.jpeg') }}"
     width="70">

<br>

_______________________

<br>

ASEP AS'ARY, S.Sos

<br>

Direktur

</td>

</tr>

</table>

</body>
</html>