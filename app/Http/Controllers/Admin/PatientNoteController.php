<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PatientNoteController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $data = $request->validate(['body' => ['required', 'string', 'min:2', 'max:5000']]);

        $patient->notes()->create($data);

        return back()->with('success', 'Note added.');
    }

    public function destroy(Patient $patient, PatientNote $note): RedirectResponse
    {
        $this->authorize('update', $patient);
        abort_unless($note->patient_id === $patient->id, 404);
        abort_unless(auth()->user()->isAdmin() || $note->user_id === auth()->id(), 403);

        $note->delete();

        return back()->with('success', 'Note removed.');
    }
}
