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
    <td><span class="value-line">{{ $data->tanggal ?? '-' }}</td>

    <td class="label">Customer Region</td>
    <td><span class="value-line">{{ $data->region ?? '-' }}</td>
</tr>

<tr>
    <td class="label">Sales Name</td>
    <td><span class="value-line">{{ $data->user->name_user ?? '-' }}</td>

    <td class="label">Request Detail</td>
    <td><span class="value-line">
        @forelse($data->details as $item)
            {{ $item->grade->name_grade ?? '-' }} 
            ({{ strtoupper($item->type) }}) 
            - {{ number_format($item->qty) }}m³<br>
        @empty
            -
        @endforelse
    </td>
    
</tr>

<tr>
    <td class="label">Customer No</td>
    <td><span class="value-line">{{ $data->customer_number ?? '-' }}</td>

    <td class="label">Phone</td>
    <td><span class="value-line">{{ $data->phone ?? '-' }}</td>
</tr>

<tr>
    <td class="label">Customer Name</td>
    <td colspan="3"><span class="value-line">{{ $data->customer_name ?? '-' }}</td>
</tr>

<tr>
    <td class="label">Address</td>
    <td colspan="3"><span class="value-line">{{ $data->address ?? '-' }}</td>
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
    <td class="label">No Identitas</td>
    <td><span class="value-line">{{ $data->no_identitas ?? '-' }}</td>

    <td class="label">NPWP</td>
    <td><span class="value-line">{{ $data->npwp ?? '-' }}</td>
</tr>

<tr>
    <td class="label">Form of Business</td>
    <td><span class="value-line">{{ $data->form_business ?? '-' }}</td>

    <td class="label">Tax Name</td>
    <td><span class="value-line">{{ $data->tax_name ?? '-' }}</td>
</tr>

<tr>
    <td class="label">Section of Business</td>
    <td><span class="value-line">{{ $data->section_business ?? '-' }}</td>

    <td class="label">Tax Address</td>
    <td><span class="value-line">{{ $data->tax_address ?? '-' }}</td>
</tr>

<tr>
    <td class="label">Address Business</td>
    <td colspan="3"><span class="value-line">{{ $data->address_business ?? '-' }}</td>
</tr>

<!-- ================= PERIZINAN ================= -->
<tr>
    <td class="label">TDP</td>
    <td><span class="value-line">{{ $data->izin_tdp ?? '-' }}</td>

    <td class="label">Effective Date</td>
    <td><span class="value-line">{{ $data->tdp_date ?? '-' }}</td>
</tr>

<tr>
    <td class="label">SIUP</td>
    <td><span class="value-line">{{ $data->izin_siup ?? '-' }}</td>

    <td class="label">Effective Date</td>
    <td><span class="value-line">{{ $data->siup_date ?? '-' }}</td>
</tr>

<tr>
    <td class="label">SIO</td>
    <td><span class="value-line">{{ $data->izin_sio ?? '-' }}</td>

    <td class="label">Effective Date</td>
    <td><span class="value-line">{{ $data->sio_date ?? '-' }}</td>
</tr>

<!-- ================= OWNER ================= -->
<tr>
    <td class="label">Owner Name</td>
    <td><span class="value-line">{{ $data->owner_name ?? '-' }}</td>

    <td class="label">Email</td>
    <td><span class="value-line">{{ $data->email ?? '-' }}</td>
</tr>

<tr>
    <td class="label">Owner Address</td>
    <td colspan="3"><span class="value-line">{{ $data->owner_address ?? '-' }}</td>
</tr>

<tr>
    <td class="label">Business Ownership</td>
    <td colspan="3"><span class="value-line">{{ ucwords(str_replace('_',' ', $data->business_ownership ?? '-')) }}</td>
</tr>

<!-- ================= PROJECT ================= -->
<tr>
    <td class="label">Office Address</td>
    <td colspan="3"><span class="value-line">{{ $data->office_address ?? '-' }}</td>
</tr>

<tr>
    <td class="label">Ongoing Project</td>
    <td colspan="3"><span class="value-line">{{ $data->ongoing_project ?? '-' }}</td>
</tr>

</table>

<br>

<!-- ================= APPROVAL ================= -->
<table class="table-border">
    <!-- HEADER -->
    <tr>
        <td class="section" width="40%">Remark / Keterangan</td>
        <td class="section" width="20%" align="center">
            Created by<br><small>Dibuat oleh</small>
        </td>
        <td class="section" width="20%" align="center">
            Approved by<br><small>Disetujui oleh</small>
        </td>
        <td class="section" width="20%" align="center">
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
        <td>
            Date: 
            {{ optional($data->approvals->where('role','wakil_direktur')->first())->approved_at 
                ? date('d-m-Y', strtotime(optional($data->approvals->where('role','wakil_direktur')->first())->approved_at)) 
                : '-' 
            }}
        </td>

        <!-- ACK -->
        <td>
            Date: 
            {{ optional($data->approvals->where('role','direktur_utama')->first())->approved_at 
                ? date('d-m-Y', strtotime(optional($data->approvals->where('role','direktur_utama')->first())->approved_at)) 
                : '-' 
            }}
        </td>
    </tr>

    <!-- TANDA TANGAN -->
    <tr>
        <td align="center" style="height:40px;">
             {{ $data->user->name_user ?? '(    )' }}<br> 
        </td>
        <td align="center">
            {{ optional($data->approvals->where('role','wakil_direktur')->first())->user->name_user ?? '-' }}
        </td>
        <td align="center">
            {{ optional($data->approvals->where('role','direktur_utama')->first())->user->name_user ?? '-' }}
        </td>
    </tr>

    <!-- NAMA + JABATAN -->
    <tr>
        <td align="center">
            <small>Sales</small>
        </td>

        <td align="center">
            <small>Wakil Direktur</small>
        </td>

        <td align="center">
            <small>Direktur Utama</small>
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