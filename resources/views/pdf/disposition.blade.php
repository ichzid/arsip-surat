<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lembar Disposisi</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 20px;
        }
        .header-container {
            border-bottom: 3px double #000;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .title-box {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 20%;
            background-color: #f0f0f0;
        }
        .half-width {
            width: 50%;
        }
        .checkbox-container {
            display: inline-block;
            margin-right: 15px;
        }
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid black;
            margin-right: 5px;
            position: relative;
            top: 2px;
        }
        .checkbox.checked {
            background-color: black;
        }
        .footer-note {
            margin-top: 30px;
            font-style: italic;
            font-size: 10px;
        }
        /* Custom Layout Fixes */
        .instruction-cell {
            vertical-align: top;
            padding-left: 15px;
        }
        .division-cell {
            vertical-align: top;
            border-right: 1px solid black;
        }
        .no-break {
            page-break-inside: avoid;
        }
        /* Header Table specific styles to remove border */
        .header-table, .header-table td {
            border: none !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-container">
            <table class="header-table">
                <tr>
                    <td style="width: 80px; text-align: left;">
                        @if(isset($settings['app_logo']) && $settings['app_logo'])
                            @php
                                $logoPath = public_path($settings['app_logo']);
                                if (!file_exists($logoPath)) {
                                    $storagePath = storage_path('app/public/' . str_replace('/storage/', '', $settings['app_logo']));
                                    if (file_exists($storagePath)) {
                                        $logoPath = $storagePath;
                                    }
                                }
                            @endphp
                            <img src="{{ $logoPath }}" style="width: 70px; height: auto;">
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <h2 style="margin: 0; font-size: 20px; text-transform: uppercase;">{{ $settings['institution_name'] ?? 'NAMA INSTANSI' }}</h2>
                        <p style="margin: 2px 0; font-size: 12px;">{{ $settings['institution_address'] ?? 'Alamat Instansi' }}</p>
                        <p style="margin: 2px 0; font-size: 12px;">
                            @if(isset($settings['institution_phone'])) Telp: {{ $settings['institution_phone'] }} @endif
                            @if(isset($settings['institution_email'])) | Email: {{ $settings['institution_email'] }} @endif
                        </p>
                    </td>
                    <td style="width: 80px;"></td> <!-- Spacer to center the text properly relative to page width -->
                </tr>
            </table>
        </div>

        <div class="title-box">LEMBAR DISPOSISI</div>

        <!-- Info Indexing -->
        <table>
            <tr>
                <td class="label">Kode</td>
                <td width="30%">{{ $letter->agenda_number }}</td>
                <td class="label">Tanggal Penyelesaian</td>
                <td>
                    @if($letter->dispositions->count() > 0 && $letter->dispositions->first()->due_date)
                        {{ \Carbon\Carbon::parse($letter->dispositions->first()->due_date)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>

        <!-- Ringkasan Surat -->
        <table>
            <tr>
                <td class="label">Asal Surat</td>
                <td colspan="3">{{ $letter->origin }}</td>
            </tr>
            <tr>
                <td class="label">Nomor Surat</td>
                <td width="30%">{{ $letter->mail_number }}</td>
                <td class="label">Tanggal Surat</td>
                <td>{{ $letter->mail_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">Perihal</td>
                <td colspan="3" style="height: 40px;">{{ $letter->subject }}</td>
            </tr>
            <tr>
                <td class="label">Diterima Tgl</td>
                <td colspan="3">{{ $letter->received_date->format('d/m/Y') }}</td>
            </tr>
        </table>

        <!-- Instruksi & Tujuan -->
        <table>
            <tr>
                <th class="half-width">Diteruskan Kepada:</th>
                <th class="half-width">Instruksi / Informasi:</th>
            </tr>
            <tr>
                <td class="division-cell" style="height: 250px;">
                    @foreach($divisions as $division)
                        @php
                            // Check if this division is in the disposition list
                            $isDisposed = $letter->dispositions->contains('to_division_id', $division->id);
                        @endphp
                        <div style="margin-bottom: 8px;">
                            <span class="checkbox {{ $isDisposed ? 'checked' : '' }}"></span>
                            {{ $division->name }}
                        </div>
                    @endforeach
                </td>
                <td class="instruction-cell">
                    @php
                        $instructions = $letter->dispositions->pluck('instruction')->filter()->toArray();
                    @endphp
                    <div style="margin-bottom: 15px;">
                        <div style="margin-bottom: 5px;"><span class="checkbox {{ in_array('Untuk ditindak lanjuti', $instructions) ? 'checked' : '' }}"></span> Untuk ditindak lanjuti</div>
                        <div style="margin-bottom: 5px;"><span class="checkbox {{ in_array('Untuk penyelesaian', $instructions) ? 'checked' : '' }}"></span> Untuk penyelesaian</div>
                        <div style="margin-bottom: 5px;"><span class="checkbox {{ in_array('Untuk diketahui', $instructions) ? 'checked' : '' }}"></span> Untuk diketahui</div>
                        <div style="margin-bottom: 5px;"><span class="checkbox {{ in_array('Untuk perhatian', $instructions) ? 'checked' : '' }}"></span> Untuk perhatian</div>
                        <div style="margin-bottom: 5px;"><span class="checkbox {{ in_array('Untuk saran/pendapat', $instructions) ? 'checked' : '' }}"></span> Untuk saran/pendapat</div>
                        <div style="margin-bottom: 5px;"><span class="checkbox {{ in_array('Harap dibicarakan dengan saya', $instructions) ? 'checked' : '' }}"></span> Harap dibicarakan dengan saya</div>
                        <div style="margin-bottom: 5px;"><span class="checkbox {{ in_array('Arsip', $instructions) ? 'checked' : '' }}"></span> Arsip</div>
                    </div>
                    <hr>
                    <div style="margin-top: 10px;">
                        <strong>Catatan Pimpinan:</strong>
                        <br><br>
                        @foreach($letter->dispositions as $disp)
                            @if($disp->notes)
                                <div style="margin-bottom: 5px;">
                                    - {{ $disp->notes }} ({{ $disp->toDivision->name }})
                                </div>
                            @endif
                        @endforeach
                    </div>
                </td>
            </tr>
        </table>

        <div class="no-break" style="float: right; text-align: center; margin-top: 10px;">
            <p>Pimpinan,</p>
            <br><br><br>
            <p>( ..................................... )</p>
        </div>
    </div>
</body>
</html>
