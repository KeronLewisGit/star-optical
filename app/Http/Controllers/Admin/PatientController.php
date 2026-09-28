<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PatientRequest;
use App\Models\ActivityLog;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Patient::class);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(Patient::STATUSES)],
        ]);

        $patients = Patient::withCount(['bookings', 'prescriptions'])
            ->search($filters['q'] ?? null)
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->orderBy('last_name')->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.patients.index', compact('patients', 'filters'));
    }

    public function create(): View
    {
        $this->authorize('create', Patient::class);

        return view('admin.patients.form', ['patient' => new Patient(['status' => 'active'])]);
    }

    public function store(PatientRequest $request): RedirectResponse
    {
        $this->authorize('create', Patient::class);

        $patient = Patient::create($request->patientData());

        return redirect()->route('admin.patients.show', $patient)->with('success', "Patient {$patient->patient_number} created.");
    }

    public function show(Patient $patient): View
    {
        $this->authorize('view', $patient);

        $patient->load(['bookings.assignee', 'prescriptions.creator', 'notes.author', 'creator']);

        return view('admin.patients.show', [
            'patient' => $patient,
            'history' => ActivityLog::where('subject_type', Patient::class)->where('subject_id', $patient->id)
                ->with('user')->latest('created_at')->limit(15)->get(),
        ]);
    }

    public function edit(Patient $patient): View
    {
        $this->authorize('update', $patient);

        return view('admin.patients.form', compact('patient'));
    }

    public function update(PatientRequest $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $patient->update($request->patientData());

        return redirect()->route('admin.patients.show', $patient)->with('success', 'Patient updated.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $this->authorize('delete', $patient);

        $patient->delete();

        return redirect()->route('admin.patients.index')->with('success', 'Patient archived.');
    }
}
