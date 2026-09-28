<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    private function rules(): array
    {
        $measure = ['nullable', 'string', 'max:10', 'regex:/^[\+\-]?\d{1,3}(\.\d{1,2})?$/'];

        return [
            'exam_date' => ['required', 'date', 'before_or_equal:today'],
            'od_sphere' => $measure, 'od_cylinder' => $measure, 'od_axis' => ['nullable', 'integer', 'between:0,180'], 'od_add' => $measure,
            'os_sphere' => $measure, 'os_cylinder' => $measure, 'os_axis' => ['nullable', 'integer', 'between:0,180'], 'os_add' => $measure,
            'pd' => ['nullable', 'string', 'max:20', 'regex:/^[\d\.\/\s]+$/'],
            'examined_by' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $data = array_map(fn ($v) => $v === '' ? null : $v, $request->validate($this->rules()));
        $patient->prescriptions()->create($data);

        return back()->with('success', 'Prescription saved.');
    }

    public function update(Request $request, Patient $patient, Prescription $prescription): RedirectResponse
    {
        $this->authorize('update', $patient);
        abort_unless($prescription->patient_id === $patient->id, 404);

        $data = array_map(fn ($v) => $v === '' ? null : $v, $request->validate($this->rules()));
        $prescription->update($data);

        return back()->with('success', 'Prescription updated.');
    }

    public function destroy(Patient $patient, Prescription $prescription): RedirectResponse
    {
        $this->authorize('update', $patient);
        abort_unless($prescription->patient_id === $patient->id, 404);
        abort_unless(auth()->user()->isAdmin(), 403);

        $prescription->delete();

        return back()->with('success', 'Prescription removed.');
    }
}
