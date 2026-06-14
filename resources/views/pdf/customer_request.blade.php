<!DOCTYPE html>
<html>
<head>
<style>

body {
    font-family: Arial, sans-serif;
    font-size: 11px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

td, th {
    border: 1px solid #000;
    padding: 4px;
    vertical-align: top;
}

.title {
    text-align: center;
    font-weight: bold;
    font-size: 14px;
}

.section {
    background: #e5e5e5;
    font-weight: bold;
    text-align: center;
}

.label {
    width: 20%;
    font-weight: bold;
}

.value {
    width: 30%;
}
</style>
</head>

    @php
        $wadir = $data->approvals->where('role','wakil_direktur')->first();
        $dirut = $data->approvals->where('role','direktur_utama')->first();
    @endphp

<body>

<!-- ================= HEADER ================= -->
<table class="table-border">
<tr>
    <td width="10%">
        <img src="{{ public_path('asset/image/Logo_aston.png') }}" width="80">
    </td>

    <td class="title">
        CUSTOMER REQUEST FORM <br>
        FORM PENGAJUAN PELANGGAN
    </td>

    <td width="20%" style="text-align:center;">
        FR-SLS-01-A <br>
        Rev: 00 <br>
        CR/YY/MM/XXX
    </td>
</tr>
</table>

<br>

<!-- ================= IDENTITAS SALES ================= -->
<table class="table-clean">
<tr>
    <td colspan="4" class="section">
        Identitas Sales / Sales Identity
    </td>
</tr>

<tr>
    <td class="label">Creation Date</td>
    <td><span class="value-line">{{ $data->tanggal ?? '-' }}</span></td>

    <td class="label">Customer Region</td>
    <td><span class="value-line">{{ $data->region ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">Sales Name</td>
    <td><span class="value-line">{{ $data->user->name_user ?? '-' }}</span></td>

    <td class="label">Request Detail</td>
    <td><span class="value-line">
        @forelse($data->details as $item)
            {{ $item->grade->name_grade ?? '-' }} 
            ({{ strtoupper($item->type) }}) 
            - {{ number_format($item->qty) }}m³<br>
        @empty
            -
        @endforelse
    </span>
    </td>
    
</tr>

<tr>
    <td class="label">NIK</td>
    <td><span class="value-line">{{ $data->user->nik ?? '-' }}</span></td>

    <td class="label">Customer No</td>
    <td><span class="value-line">{{ $data->customer_number ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">Sales Code</td>
    <td><span class="value-line">{{ $data->user->id_user ?? '-' }}</span></td>

    <td class="label">Delivery Distance</td>
    <td><span class="value-line">{{ $data->delivery_distance ? $data->delivery_distance . ' km' : '-' }}</span></td>
</tr>

<tr>
    <td class="label">Delivery Fee</td>
    <td><span class="value-line">{{ $data->delivery_fee > 0 ? 'Rp ' . number_format($data->delivery_fee, 0, ',', '.') : 'Rp 0' }}</span></td>

    <td class="label">Grand Total</td>
    <td><span class="value-line" style="font-weight: bold;">Rp {{ number_format($data->grand_total > 0 ? $data->grand_total : $data->details->sum('total'), 0, ',', '.') }}</span></td>
</tr>

</table>

<br>

<!-- ================= CUSTOMER PROFILE ================= -->
<table class="table-clean">

<tr>
    <td colspan="4" class="section">
        Profil Pelanggan / Customer Profile
    </td>
</tr>

<tr>
    <td class="label">Customer Name</td>
    <td colspan="3"><span class="value-line">{{ $data->customer_name ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">No Identitas</td>
    <td colspan="3"><span class="value-line">{{ $data->no_identitas ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">Form of Business</td>
    <td colspan="3"><span class="value-line">{{ $data->form_business ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">Section of Business</td>
    <td colspan="3"><span class="value-line">{{ $data->section_business ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">Address Business</td>
    <td colspan="3"><span class="value-line">{{ $data->address_business ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">Address</td>
    <td colspan="3"><span class="value-line">{{ $data->address ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">Phone</td>
    <td colspan="3"><span class="value-line">{{ $data->phone ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">NPWP</td>
    <td colspan="3"><span class="value-line">{{ $data->npwp ?? '-' }}</span></td>
</tr>

<tr>

    <td class="label">Tax Name</td>
    <td colspan="3"><span class="value-line">{{ $data->tax_name ?? '-' }}</span></td>
</tr>

<tr>

    <td class="label">Tax Address</td>
    <td colspan="3"><span class="value-line">{{ $data->tax_address ?? '-' }}</span></td>
</tr>

<!-- ================= PERIZINAN ================= -->
<tr>
    <td colspan="4" class="label">Permit No.</td>
</tr>
<tr>
    <td class="label">TDP</td>
    <td><span class="value-line">{{ $data->izin_tdp ?? '-' }}</span></td>

    <td class="label">Effective Date</td>
    <td><span class="value-line">{{ $data->tdp_date ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">SIUP</td>
    <td><span class="value-line">{{ $data->izin_siup ?? '-' }}</span></td>

    <td class="label">Effective Date</td>
    <td><span class="value-line">{{ $data->siup_date ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">SIO</td>
    <td><span class="value-line">{{ $data->izin_sio ?? '-' }}</span></td>

    <td class="label">Effective Date</td>
    <td><span class="value-line">{{ $data->sio_date ?? '-' }}</span></td>
</tr>

<!-- ================= OWNER ================= -->
<tr>
    <td class="label">Owner Name</td>
    <td colspan="3"><span class="value-line">{{ $data->owner_name ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">Owner Address</td>
    <td colspan="3"><span class="value-line">{{ $data->owner_address ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">Email</td>
    <td colspan="3"><span class="value-line">{{ $data->email ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">Business Ownership</td>
    <td colspan="3"><span class="value-line">{{ ucwords(str_replace('_',' ', $data->business_ownership ?? '-')) }}</span></td>
</tr>

<!-- ================= PROJECT ================= -->
<tr>
    <td class="label">Main Office Address</td>
    <td colspan="3"><span class="value-line">{{ $data->office_address ?? '-' }}</span></td>
</tr>

<tr>
    <td class="label">State of ongoing Project</td>
    <td colspan="3"><span class="value-line">{{ $data->ongoing_project ?? '-' }}</span></td>
</tr>

</table>

<br>

<!-- ================= APPROVAL ================= -->
<table class="table-border">
    <!-- HEADER -->
    <tr>
        <td class="section" width="35%">Remark / Keterangan</td>
        <td class="section" width="40%" align="center">
            Created by<br><small>Dibuat oleh</small>
        </td>
        <td class="section" width="40%" align="center">
            Approved by<br><small>Disetujui oleh</small>
        </td>
        <td class="section" width="40%" align="center">
            Acknowledged by<br><small>Diketahui oleh</small>
        </td>
    </tr>

    <!-- CONTENT AREA -->
    <tr>
        <!-- REMARK -->
        <td rowspan="3" style="height:120px;">
            {{ $data->note ?? '-' }}
        </td>

        <!-- CREATED -->
        <td style="height:100px;">
            Date: {{ $data->created_at ? date('d-m-Y', strtotime($data->created_at)) : '-' }}
        </td>

        <!-- APPROVED -->
        <td style="text-align:center;">
            Date:<br>
        
            @php
                $approvedDate = $wadir->approved_at ?? $dirut->approved_at ?? null;
            @endphp
            {{ $approvedDate ? date('d-m-Y', strtotime($approvedDate)) : '-' }}
        
            @if(($wadir && $wadir->approved_at) || ($dirut && $dirut->approved_at))
                <br>
                <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('asset/image/Qr_wadir.jpeg'))) }}" 
                     style="width:80px; margin-top:5px;">
            @endif
        </td>

        <!-- ACK -->
        <td style="text-align:center;">
            Date:<br>
        
            @php
                $ackDate = $dirut->approved_at ?? $wadir->approved_at ?? null;
            @endphp
            {{ $ackDate ? date('d-m-Y', strtotime($ackDate)) : '-' }}
        
            @if(($wadir && $wadir->approved_at) || ($dirut && $dirut->approved_at))
                <br>
                <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('asset/image/Qr_dirut.jpeg'))) }}" 
                     style="width:80px; margin-top:5px;">
            @endif
        </td>
    </tr>

    <!-- TANDA TANGAN -->
    <tr>
        <td align="center" style="height:45px; vertical-align:bottom;">
        <div class="nama">{{ $data->user->name_user ?? '(    )' }}</div>
    </td>

    <td align="center" style="height:45px; vertical-align:bottom;">
        <div class="nama">Ronald Asmerico Marpaung, S.T</div>
    </td>

    <td align="center" style="height:45px; vertical-align:bottom;">
        <div class="nama">ASEP AS'ARY, S.Sos</div>
    </td>
    </tr>

    <!-- NAMA + JABATAN -->
   <tr>
    <td align="center">
        <div class="jabatan">Sales</div>
    </td>

    <td align="center">
        <div class="jabatan">Wakil Direktur</div>
    </td>

    <td align="center">
        <div class="jabatan">Direktur Utama</div>
    </td>
    </tr>
</table>

</body>
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 11px;
    }
    
    /* ===================== */
    /* DEFAULT TABLE (TANPA BORDER DALAM) */
    /* ===================== */
    .table-clean {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
    }
    
    .table-clean td,
    .table-clean th {
        border: none;
        padding: 4px;
        vertical-align: top;
    }
    
    /* ===================== */
    /* APPROVAL TABLE (FULL BORDER) */
    /* ===================== */
    .table-border {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table-border td,
    .table-border th {
        border: 1px solid #000;
        padding: 4px;
    }
    
    /* ===================== */
    .title {
        text-align: center;
        font-weight: bold;
        font-size: 14px;
    }
    
    .section {
        background: #e5e5e5;
        font-weight: bold;
        text-align: center;
    }
    
    .label {
        width: 20%;
        font-weight: bold;
    }

    .value-line {
    display: inline-block;
    width: 100%;
    border-bottom: 1px solid #000;
    min-height: 14px;
    }
    </style>

</html>