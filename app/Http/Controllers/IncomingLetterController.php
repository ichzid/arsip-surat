<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use App\Models\User;
use App\Models\Division;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;

class IncomingLetterController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = IncomingLetter::with(['dispositions.toDivision', 'dispositions.fromDivision'])
            ->visibleTo($user);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'like', '%'.$request->search.'%')
                  ->orWhere('mail_number', 'like', '%'.$request->search.'%')
                  ->orWhere('origin', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->start_date) {
            $query->whereDate('mail_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('mail_date', '<=', $request->end_date);
        }

        $letters = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('IncomingLetters/Index', [
            'letters' => $letters,
            'filters' => $request->only(['search', 'start_date', 'end_date']),
            'can' => [
                'create' => $user->can('create', IncomingLetter::class),
                'edit' => $user->hasRole(['Admin', 'Operator Divisi Umum']),
                'delete' => $user->hasRole(['Admin', 'Operator Divisi Umum']),
            ],
        ]);
    }

    public function create()
    {
        // Authorization check via Policy
        $this->authorize('create', IncomingLetter::class);

        return Inertia::render('IncomingLetters/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', IncomingLetter::class);
        $user = Auth::user();

        $validated = $request->validate([
            'mail_number' => 'required|string|max:255',
            'mail_date' => 'nullable|date',
            'received_date' => 'nullable|date',
            'origin' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB, PDF only
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('incoming_letters', 'public');
        }

        // Generate Agenda Number (Format: AGN-YYYYMMDD-XXXX)
        $dateCode = now()->format('Ymd');
        $count = IncomingLetter::whereDate('created_at', today())->count() + 1;
        $agendaNumber = 'AGN-' . $dateCode . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        IncomingLetter::create([
            'agenda_number' => $agendaNumber,
            'mail_number' => $validated['mail_number'],
            'mail_date' => $validated['mail_date'] ?? now()->toDateString(),
            'received_date' => $validated['received_date'] ?? now()->toDateString(),
            'origin' => $validated['origin'],
            'subject' => $validated['subject'],
            'file_path' => $filePath,
            'status' => 'new',
            'created_by' => $user->id,
        ]);

        return redirect()->route('incoming-letters.index')->with('success', 'Surat masuk berhasil dicatat.');
    }

    public function edit(IncomingLetter $incomingLetter)
    {
        $this->authorize('update', $incomingLetter);

        return Inertia::render('IncomingLetters/Edit', [
            'letter' => $incomingLetter,
        ]);
    }

    public function update(Request $request, IncomingLetter $incomingLetter)
    {
        $this->authorize('update', $incomingLetter);

        $validated = $request->validate([
            'mail_number' => 'required|string|max:255',
            'mail_date' => 'required|date',
            'received_date' => 'required|date',
            'origin' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB, PDF only
        ]);

        if ($request->hasFile('file')) {
            if ($incomingLetter->file_path) {
                Storage::disk('public')->delete($incomingLetter->file_path);
            }
            $incomingLetter->file_path = $request->file('file')->store('incoming_letters', 'public');
        }

        $incomingLetter->update([
            'mail_number' => $validated['mail_number'],
            'mail_date' => $validated['mail_date'],
            'received_date' => $validated['received_date'],
            'origin' => $validated['origin'],
            'subject' => $validated['subject'],
        ]);

        return redirect()->route('incoming-letters.index')->with('success', 'Surat masuk berhasil diperbarui.');
    }

    public function destroy(IncomingLetter $incomingLetter)
    {
        $this->authorize('delete', $incomingLetter);

        if ($incomingLetter->file_path) {
            Storage::disk('public')->delete($incomingLetter->file_path);
        }

        $incomingLetter->delete();

        return redirect()->route('incoming-letters.index')->with('success', 'Surat masuk berhasil dihapus.');
    }

    public function show(IncomingLetter $incomingLetter)
    {
        $this->authorize('view', $incomingLetter);

        $user = Auth::user();
        
        // Logic filter riwayat disposisi:
        // 1. Divisi Umum/Admin/Pimpinan: Lihat semua riwayat
        // 2. Divisi Lain: Hanya lihat riwayat yang berkaitan dengan divisinya (Tujuan atau Pengirim)
        
        $isPrivileged = $user->hasRole(['Admin', 'Pimpinan', 'Operator Divisi Umum']) 
                        || ($user->division && $user->division->code === 'UMUM');

        $incomingLetter->load(['creator']);

        if ($isPrivileged) {
            $incomingLetter->load(['dispositions.toDivision', 'dispositions.fromDivision', 'dispositions.creator']);
        } else {
            $incomingLetter->load(['dispositions' => function ($query) use ($user) {
                $query->where(function($q) use ($user) {
                    $q->where('to_division_id', $user->division_id)
                      ->orWhere('from_division_id', $user->division_id);
                })->with(['toDivision', 'fromDivision', 'creator']);
            }]);
        }

        return Inertia::render('IncomingLetters/Show', [
            'letter' => $incomingLetter,
            'divisions' => Division::where('active', true)->get(),
            'auth' => [
                'user' => $user->load('roles'),
            ]
        ]);
    }

    public function printDisposition(IncomingLetter $letter)
    {
        $this->authorize('view', $letter);

        // Load necessary relations
        $letter->load(['dispositions.toDivision']);

        $divisions = Division::where('active', true)->get();

        // Load settings for PDF header
        $settings = Setting::all()->mapWithKeys(function ($item) {
            return [$item->key => $item->value];
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.disposition', [
            'letter' => $letter,
            'divisions' => $divisions,
            'settings' => $settings,
        ])->setPaper('a4', 'portrait')->setOption(['isRemoteEnabled' => true]);

        return $pdf->stream('Lembar_Disposisi_' . str_replace('/', '-', $letter->agenda_number) . '.pdf');
    }
}
