<!DOCTYPE html>
<html>
<head>
<style>
body {
    font-family: Arial, sans-serif;
    font-size: 11px;
    color: #333;
    line-height: 1.4;
}

table {
    width: 100%;
    border-collapse: collapse;
}

td, th {
    padding: 6px;
    vertical-align: top;
}

.table-header {
    border: 1px solid #333;
    margin-bottom: 20px;
}

.table-header td {
    border: 1px solid #333;
}

.title {
    text-align: center;
    font-weight: bold;
    font-size: 15px;
    text-transform: uppercase;
}

.table-info {
    width: 100%;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    background-color: #fdfdfd;
}

.table-info td {
    border-bottom: 1px solid #eee;
}

.table-info tr:last-child td {
    border-bottom: none;
}

.label {
    width: 25%;
    font-weight: bold;
    color: #555;
    background-color: #f7f7f7;
    border-right: 1px solid #eee;
}

.value {
    width: 75%;
}

.section-title {
    background-color: #e62a2a;
    color: #fff;
    font-weight: bold;
    padding: 6px 10px;
    font-size: 12px;
    text-transform: uppercase;
    margin-top: 15px;
    margin-bottom: 5px;
    border-radius: 4px;
}

.table-details {
    width: 100%;
    margin-top: 10px;
    margin-bottom: 20px;
    border: 1px solid #333;
}

.table-details th {
    background-color: #f2f2f2;
    font-weight: bold;
    text-align: center;
    border: 1px solid #333;
}

.table-details td {
    border: 1px solid #333;
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

.table-signatures {
    width: 100%;
    margin-top: 30px;
}

.table-signatures td {
    text-align: center;
    border: none;
}

.signature-space {
    height: 70px;
}

.signature-name {
    font-weight: bold;
    text-decoration: underline;
}

.signature-role {
    font-size: 10px;
    color: #666;
}

.maps-link {
    color: #2b6cb0;
    text-decoration: underline;
    font-size: 10px;
}
</style>
</head>
<body>

<!-- ================= HEADER ================= -->
<table class="table-header">
<tr>
    <td width="15%" align="center">
        @if(file_exists(public_path('asset/image/Logo_aston.png')))
            <img src="{{ public_path('asset/image/Logo_aston.png') }}" width="70">
        @else
            <div style="font-weight: bold; font-size: 14px; color: #b91c1c;">ASTON</div>
        @endif
    </td>
    <td class="title" align="center" style="vertical-align: middle;">
        SURAT PERINTAH KERJA (SPK)<br>
        <span style="font-size: 11px; font-weight: normal; text-transform: none; color: #555;">Koran Operasional Kepala Plant / Mixing & Delivery Order</span>
    </td>
    <td width="25%" style="font-size: 10px; vertical-align: middle;">
        <strong>No. SPK:</strong> SPK-{{ $data->request_code }}<br>
        <strong>Tanggal Terbit:</strong> {{ date('d-m-Y') }}<br>
        <strong>Status Order:</strong> {{ strtoupper($data->status) }}
    </td>
</tr>
</table>

<!-- ================= INFORMASI PEKERJAAN ================= -->
<div class="section-title">Informasi Pengiriman & Pekerjaan</div>
<table class="table-info">
<tr>
    <td class="label">Nama Customer</td>
    <td class="value">{{ $data->customer_name }}</td>
</tr>
<tr>
    <td class="label">No. Telepon / HP</td>
    <td class="value">{{ $data->phone ?? '-' }}</td>
</tr>
<tr>
    <td class="label">Alamat Proyek / Tujuan</td>
    <td class="value">
        {{ $data->address ?? '-' }}
        @if($data->delivery_latitude && $data->delivery_longitude)
            <br>
            <span style="font-weight: bold; color: #444;">Koordinat:</span> {{ $data->delivery_latitude }}, {{ $data->delivery_longitude }}
            <br>
            <a href="https://www.google.com/maps/search/?api=1&query={{ $data->delivery_latitude }},{{ $data->delivery_longitude }}" class="maps-link" target="_blank">
                Buka di Google Maps
            </a>
        @endif
    </td>
</tr>
<tr>
    <td class="label">Nama Proyek / Pekerjaan</td>
    <td class="value">{{ $data->ongoing_project ?? '-' }}</td>
</tr>
<tr>
    <td class="label">Jadwal Pengiriman</td>
    <td class="value" style="font-weight: bold; font-size: 12px; color: #b91c1c;">
        {{ $data->schedule_date ? date('d-m-Y', strtotime($data->schedule_date)) : 'Belum Dijadwalkan' }}
    </td>
</tr>
<tr>
    <td class="label">Catatan Operasional</td>
    <td class="value" style="font-style: italic;">{{ $data->note ?? 'Tidak ada catatan khusus.' }}</td>
</tr>
</table>

<!-- ================= RINCIAN BETON & VOLUME ================= -->
<div class="section-title">Rincian Beton & Kebutuhan Mixing</div>
<table class="table-details">
<thead>
    <tr>
        <th width="5%">No</th>
        <th width="55%">Mutu Beton / Grade</th>
        <th width="20%">Tipe Slump</th>
        <th width="20%">Volume Kebutuhan (m³)</th>
    </tr>
</thead>
<tbody>
    @php $totalQty = 0; @endphp
    @forelse($data->details as $index => $item)
        @php $totalQty += $item->qty; @endphp
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td style="font-weight: bold;">{{ $item->grade->name_grade ?? 'Mutu tidak diketahui' }}</td>
            <td class="text-center uppercase">{{ $item->type }}</td>
            <td class="text-center font-semibold">{{ number_format($item->qty, 2, ',', '.') }} m³</td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center" style="font-style: italic; color: #999;">Tidak ada rincian beton.</td>
        </tr>
    @endforelse
    <tr style="background-color: #f9f9f9; font-weight: bold;">
        <td colspan="3" class="text-right">Total Volume Pengiriman:</td>
        <td class="text-center" style="font-size: 12px; color: #b91c1c;">{{ number_format($totalQty, 2, ',', '.') }} m³</td>
    </tr>
</tbody>
</table>

<!-- ================= TANDA TANGAN ================= -->
@php
    $wadir = $data->approvals->where('role','wakil_direktur')->first();
    $dirut = $data->approvals->where('role','direktur_utama')->first();
@endphp
<table class="table-signatures">
<tr>
    <td width="33%">
        <div class="signature-role">Dibuat Oleh,</div>
        <div class="signature-space"></div>
        <div class="signature-name">{{ $data->user->name_user ?? 'Sales' }}</div>
        <div class="signature-role">Sales Department</div>
    </td>
    <td width="33%">
        <div class="signature-role">Disetujui / Dikerjakan Oleh,</div>
        <div class="signature-space"></div>
        <div class="signature-name">(................................................)</div>
        <div class="signature-role">Kepala Plant / Batching Manager</div>
    </td>
    <td width="34%">
        <div class="signature-role">Diketahui / Diotorisasi Oleh,</div>
        <div class="signature-space">
            @if(($wadir && $wadir->approved_at) || ($dirut && $dirut->approved_at))
                <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('asset/image/QR_dirut.jpeg'))) }}" 
                     style="width:55px; margin-top:5px; margin-bottom:5px;">
            @endif
        </div>
        <div class="signature-name">ASEP AS'ARY, S.Sos / Ronald A. M.</div>
        <div class="signature-role">Direksi PT. Istimewa Aston Indonesia</div>
    </td>
</tr>
</table>

</body>
</html>
