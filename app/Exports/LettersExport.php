<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LettersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $collection;
    protected $type;

    public function __construct($collection, $type)
    {
        $this->collection = $collection;
        $this->type = $type;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        if ($this->type === 'incoming') {
            return [
                'No. Agenda',
                'Nomor Surat',
                'Tanggal Surat',
                'Tanggal Diterima',
                'Asal Surat',
                'Perihal',
                'Status',
            ];
        } else {
            return [
                'Nomor Surat',
                'Tanggal Surat',
                'Tujuan',
                'Perihal',
                'Divisi Pengirim',
            ];
        }
    }

    public function map($row): array
    {
        if ($this->type === 'incoming') {
            return [
                $row->agenda_number,
                $row->mail_number,
                $row->mail_date->format('d/m/Y'),
                $row->received_date->format('d/m/Y'),
                $row->origin,
                $row->subject,
                $row->status,
            ];
        } else {
            return [
                $row->mail_number,
                $row->mail_date->format('d/m/Y'),
                $row->recipient,
                $row->subject,
                $row->division ? $row->division->name : '-',
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
