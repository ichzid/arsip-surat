<?php

namespace App\Http\Controllers;

use App\Models\Disposition;
use App\Models\IncomingLetter;
use App\Models\User;
use App\Notifications\NewDispositionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class DispositionController extends Controller
{
    public function store(Request $request, IncomingLetter $letter)
    {
        $user = Auth::user();
        
        // Authorization: Only Admin, Pimpinan, or Operator Divisi Umum
        if (!$user->hasRole(['Admin', 'Pimpinan', 'Operator Divisi Umum'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'to_division_id' => 'required|exists:divisions,id',
            'notes' => 'nullable|string',
            'instruction' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $disposition = Disposition::create([
            'incoming_letter_id' => $letter->id,
            'from_division_id' => $user->division_id, // Current user's division
            'to_division_id' => $validated['to_division_id'],
            'notes' => $validated['notes'],
            'instruction' => $validated['instruction'],
            'due_date' => $validated['due_date'],
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        // Update Letter Status to 'disposition' if it was 'new'
        if ($letter->status === 'new') {
            $letter->update(['status' => 'disposition']);
        }

        // Notify users in the target division
        $targetUsers = User::where('division_id', $validated['to_division_id'])->get();
        Notification::send($targetUsers, new NewDispositionNotification($disposition));

        return back()->with('success', 'Disposisi berhasil dikirim.');
    }

    public function updateStatus(Request $request, Disposition $disposition)
    {
        $user = Auth::user();

        // Target division can update status
        if ($user->division_id !== $disposition->to_division_id && !$user->hasRole('Admin')) {
             abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,process,done',
        ]);

        $disposition->update(['status' => $validated['status']]);

        return back()->with('success', 'Status disposisi diperbarui.');
    }
}
