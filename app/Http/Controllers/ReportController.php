<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Division;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LettersExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $type = $request->input('type', 'incoming'); // incoming or outgoing
        $divisionId = $request->input('division_id');

        $data = $this->getData($startDate, $endDate, $type, $divisionId);
        
        $user = Auth::user();
        $divisions = [];
        
        // Logic filter divisi:
        // 1. Admin & Pimpinan: Bisa filter semua jenis laporan
        // 2. Operator Divisi Umum: Hanya bisa filter Surat Masuk (karena Surat Keluar hanya punya sendiri)
        // 3. Operator Divisi Lain: Tidak bisa filter (hanya lihat data sendiri)
        
        if ($user->hasRole(['Admin', 'Pimpinan'])) {
            $divisions = Division::all();
        } elseif ($user->hasRole('Operator Divisi Umum') && $type === 'incoming') {
            $divisions = Division::all();
        }

        return Inertia::render('Reports/Index', [
            'data' => $data,
            'divisions' => $divisions,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'type' => $type,
                'division_id' => $divisionId,
            ]
        ]);
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $type = $request->input('type', 'incoming');
        $divisionId = $request->input('division_id');

        $data = $this->getData($startDate, $endDate, $type, $divisionId);
        $fileName = 'Laporan_Surat_' . ucfirst($type) . '_' . $startDate . '_sd_' . $endDate . '.xlsx';

        return Excel::download(new LettersExport($data, $type), $fileName);
    }

    private function getData($startDate, $endDate, $type, $divisionId = null)
    {
        $user = Auth::user();

        if ($type === 'incoming') {
            $query = IncomingLetter::visibleTo($user)
                ->whereBetween('mail_date', [$startDate, $endDate]);

            if ($divisionId) {
                $query->whereHas('dispositions', function ($q) use ($divisionId) {
                    $q->where('to_division_id', $divisionId);
                });
            }

            return $query->latest('mail_date')->get();
        } else {
            $query = OutgoingLetter::visibleTo($user)
                ->whereBetween('mail_date', [$startDate, $endDate])
                ->with(['division', 'creator']);

            if ($divisionId) {
                $query->where('division_id', $divisionId);
            }

            return $query->latest('mail_date')->get();
        }
    }
}
