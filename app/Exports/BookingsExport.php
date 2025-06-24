<?php

namespace App\Exports;

use App\Models\Absen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class BookingsExport implements FromCollection, WithHeadings, WithColumnWidths, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Absen::with(['dutyOfficer']); 

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('tanggal', [$this->filters['start_date'], $this->filters['end_date']]);
        } else {
            $query->whereDate('tanggal', now()->toDateString());
        }

        return $query->get()->map(function ($item) {
            return [
                'Nama' => $item->name,
                'Tanggal' => $item->tanggal,
                'Status' => $item->status,
                'Duty Officer' => $item->dutyOfficer->nama_do ?? 'N/A',
                // 'Tanda Tangan' => $item->signature ? 'data:image/png;base64,' . base64_encode($item->signature) : 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Tanggal',
            'Status',
            'Duty Officer',
            // 'Tanda Tangan',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30, // Kolom Nama
            'B' => 27, // Kolom Tanggal
            'C' => 15, // Kolom Status
            'D' => 30, // Kolom Duty Officer
            // 'E' => 15, // Kolom Tanda Tangan
        ];
    }
}