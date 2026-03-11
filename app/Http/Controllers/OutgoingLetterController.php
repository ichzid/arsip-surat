<?php

namespace App\Http\Controllers;

use App\Models\OutgoingLetter;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class OutgoingLetterController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = OutgoingLetter::with(['division', 'creator'])
            ->visibleTo($user);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'like', '%'.$request->search.'%')
                  ->orWhere('mail_number', 'like', '%'.$request->search.'%')
                  ->orWhere('recipient', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->start_date) {
            $query->whereDate('mail_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('mail_date', '<=', $request->end_date);
        }

        $letters = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('OutgoingLetters/Index', [
            'letters' => $letters,
            'filters' => $request->only(['search', 'start_date', 'end_date']),
            'can' => [
                'create' => $user->can('create', OutgoingLetter::class),
                'manage_all' => $user->hasRole('Admin'),
            ],
            'current_user_id' => $user->id,
        ]);
    }

    public function create()
    {
        $this->authorize('create', OutgoingLetter::class);
        return Inertia::render('OutgoingLetters/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', OutgoingLetter::class);
        $user = Auth::user();

        $validated = $request->validate([
            'mail_number' => 'required|string|max:255|unique:outgoing_letters,mail_number',
            'recipient' => 'required|string|max:255',
            'mail_date' => 'nullable|date',
            'subject' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB, PDF only
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('outgoing_letters', 'public');
        }

        OutgoingLetter::create([
            'mail_number' => $validated['mail_number'],
            'recipient' => $validated['recipient'],
            'mail_date' => $validated['mail_date'] ?? now()->toDateString(),
            'subject' => $validated['subject'],
            'division_id' => $user->division_id, // Default to user's division
            'file_path' => $filePath,
            'created_by' => $user->id,
        ]);

        return redirect()->route('outgoing-letters.index')->with('success', 'Surat keluar berhasil dicatat.');
    }

    public function show(OutgoingLetter $outgoingLetter)
    {
        $this->authorize('view', $outgoingLetter);

        $outgoingLetter->load(['division', 'creator']);

        return Inertia::render('OutgoingLetters/Show', [
            'letter' => $outgoingLetter,
        ]);
    }

    public function edit(OutgoingLetter $outgoingLetter)
    {
        $this->authorize('update', $outgoingLetter);
        
        return Inertia::render('OutgoingLetters/Edit', [
            'letter' => $outgoingLetter,
        ]);
    }

    public function update(Request $request, OutgoingLetter $outgoingLetter)
    {
        $this->authorize('update', $outgoingLetter);

        $validated = $request->validate([
            'mail_number' => 'required|string|max:255|unique:outgoing_letters,mail_number,'.$outgoingLetter->id,
            'recipient' => 'required|string|max:255',
            'mail_date' => 'required|date',
            'subject' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB, PDF only
        ]);

        if ($request->hasFile('file')) {
            if ($outgoingLetter->file_path) {
                Storage::disk('public')->delete($outgoingLetter->file_path);
            }
            $outgoingLetter->file_path = $request->file('file')->store('outgoing_letters', 'public');
        }

        $outgoingLetter->update([
            'mail_number' => $validated['mail_number'],
            'recipient' => $validated['recipient'],
            'mail_date' => $validated['mail_date'],
            'subject' => $validated['subject'],
        ]);

        return redirect()->route('outgoing-letters.index')->with('success', 'Surat keluar berhasil diperbarui.');
    }

    public function destroy(OutgoingLetter $outgoingLetter)
    {
        $this->authorize('delete', $outgoingLetter);

        if ($outgoingLetter->file_path) {
            Storage::disk('public')->delete($outgoingLetter->file_path);
        }

        $outgoingLetter->delete();

        return redirect()->route('outgoing-letters.index')->with('success', 'Surat keluar berhasil dihapus.');
    }
}
