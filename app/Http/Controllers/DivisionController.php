<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class DivisionController extends Controller
{
    public function __construct()
    {
        // Only Admin can manage divisions
        // We can use middleware or check in methods. Middleware is cleaner but let's do check here for clarity.
    }

    private function authorizeAdmin()
    {
        if (!Auth::user()->hasRole('Admin')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = Division::query();

        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('code', 'like', '%'.$request->search.'%');
        }

        $divisions = $query->paginate(10)->withQueryString();

        return Inertia::render('Divisions/Index', [
            'divisions' => $divisions,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        $this->authorizeAdmin();
        return Inertia::render('Divisions/Create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:divisions,code',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        Division::create($validated);

        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function edit(Division $division)
    {
        $this->authorizeAdmin();
        return Inertia::render('Divisions/Edit', [
            'division' => $division
        ]);
    }

    public function update(Request $request, Division $division)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:10', Rule::unique('divisions')->ignore($division->id)],
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $division->update($validated);

        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Division $division)
    {
        $this->authorizeAdmin();
        
        // Check if division has users or letters related
        if ($division->users()->exists() || $division->dispositionsReceived()->exists() || $division->outgoingLetters()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus divisi yang memiliki data relasi (User/Surat/Disposisi). Nonaktifkan saja.');
        }

        $division->delete();

        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil dihapus.');
    }
}
