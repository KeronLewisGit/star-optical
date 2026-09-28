<x-admin-layout :title="$patient->full_name">
    <div class="flex flex-wrap items-center gap-3">
        <span class="font-mono text-sm text-gray-500">{{ $patient->patient_number }}</span>
        <x-badge :status="$patient->status" />
        <span class="text-sm text-gray-500">Added {{ $patient->created_at->format('j M Y') }}{{ $patient->creator ? ' by '.$patient->creator->name : '' }}</span>
        <div class="ml-auto flex gap-2">
            <a href="{{ \App\Models\Setting::whatsappLink('') }}" target="_blank" rel="noopener" class="btn-secondary btn-sm"><i class="fa-brands fa-whatsapp text-wa"></i> WhatsApp</a>
            <a href="tel:{{ $patient->phone }}" class="btn-secondary btn-sm"><i class="fa-solid fa-phone"></i> Call</a>
            <a href="{{ route('admin.patients.edit', $patient) }}" class="btn-primary btn-sm"><i class="fa-solid fa-pen"></i> Edit</a>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Details --}}
            <div class="card"><div class="card-body">
                <h2 class="card-title">Details</h2>
                <dl class="mt-3 grid sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-gray-500">Phone</dt><dd class="font-medium">{{ $patient->formatted_phone }}@if($patient->phone_alt) · {{ $patient->phone_alt }}@endif</dd></div>
                    <div><dt class="text-gray-500">Email</dt><dd class="font-medium">{{ $patient->email ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Date of birth</dt><dd class="font-medium">{{ $patient->date_of_birth?->format('j F Y') ?? '—' }}@if($patient->date_of_birth) <span class="text-gray-500">({{ $patient->date_of_birth->age }} yrs)</span>@endif</dd></div>
                    <div><dt class="text-gray-500">Gender</dt><dd class="font-medium">{{ $patient->gender ? ucfirst(str_replace('_', ' ', $patient->gender)) : '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-gray-500">Address</dt><dd class="font-medium">{{ $patient->address ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Emergency contact</dt><dd class="font-medium">{{ $patient->emergency_contact_name ?? '—' }}@if($patient->emergency_contact_phone) · {{ $patient->emergency_contact_phone }}@endif</dd></div>
                    <div><dt class="text-gray-500">Insurance</dt><dd class="font-medium">{{ $patient->insurance_provider ?? '—' }}</dd></div>
                </dl>
                @if($patient->allergies || $patient->medical_notes)
                <div class="mt-4 grid sm:grid-cols-2 gap-3 text-sm">
                    @if($patient->allergies)<div class="rounded-lg bg-red-50 p-3"><p class="text-xs uppercase text-red-700 mb-1"><i class="fa-solid fa-triangle-exclamation"></i> Allergies</p>{{ $patient->allergies }}</div>@endif
                    @if($patient->medical_notes)<div class="rounded-lg bg-gray-50 p-3"><p class="text-xs uppercase text-gray-500 mb-1"><i class="fa-solid fa-lock"></i> Medical notes</p><div class="whitespace-pre-line">{{ $patient->medical_notes }}</div></div>@endif
                </div>
                @endif
            </div></div>

            {{-- Prescriptions --}}
            <div class="card"><div class="card-body">
                <div class="flex items-center justify-between"><h2 class="card-title">Prescriptions</h2><button data-toggle="#rxForm" class="btn-secondary btn-sm"><i class="fa-solid fa-plus"></i> Add prescription</button></div>

                <form id="rxForm" method="POST" action="{{ route('admin.patients.prescriptions.store', $patient) }}" class="{{ $errors->has('exam_date') || $errors->has('od_sphere') ? '' : 'hidden' }} mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4 space-y-3" data-once>
                    @csrf
                    <div class="grid sm:grid-cols-3 gap-3">
                        <x-form.input name="exam_date" label="Exam date" type="date" :value="date('Y-m-d')" required />
                        <x-form.input name="examined_by" label="Examined by" />
                        <x-form.input name="pd" label="PD (mm)" placeholder="e.g. 63 or 31/32" />
                    </div>
                    <table class="table text-center">
                        <thead><tr><th></th><th>Sphere</th><th>Cylinder</th><th>Axis</th><th>Add</th></tr></thead>
                        <tbody>
                        @foreach(['od' => 'OD (right)', 'os' => 'OS (left)'] as $eye => $label)
                            <tr><td class="text-left font-medium">{{ $label }}</td>
                                <td><input name="{{ $eye }}_sphere" class="form-input text-center" placeholder="-1.25" value="{{ old($eye.'_sphere') }}"></td>
                                <td><input name="{{ $eye }}_cylinder" class="form-input text-center" placeholder="-0.50" value="{{ old($eye.'_cylinder') }}"></td>
                                <td><input name="{{ $eye }}_axis" class="form-input text-center" placeholder="180" value="{{ old($eye.'_axis') }}"></td>
                                <td><input name="{{ $eye }}_add" class="form-input text-center" placeholder="+2.00" value="{{ old($eye.'_add') }}"></td></tr>
                        @endforeach
                        </tbody>
                    </table>
                    <x-form.textarea name="notes" label="Notes" rows="2" />
                    <button class="btn-primary btn-sm">Save prescription</button>
                </form>

                <div class="mt-4 space-y-3">
                    @forelse($patient->prescriptions as $rx)
                        <div class="rounded-lg border border-gray-200 p-3 text-sm">
                            <div class="flex flex-wrap items-center gap-2"><strong>{{ $rx->exam_date->format('j M Y') }}</strong>@if($rx->examined_by)<span class="text-gray-500">by {{ $rx->examined_by }}</span>@endif @if($rx->pd)<span class="badge bg-gray-100 text-gray-700">PD {{ $rx->pd }}</span>@endif
                                @if(auth()->user()->isAdmin())<form method="POST" action="{{ route('admin.patients.prescriptions.destroy', [$patient, $rx]) }}" class="ml-auto" data-confirm="Remove this prescription?">@csrf @method('DELETE')<button class="text-xs text-red-600 hover:underline">Remove</button></form>@endif
                            </div>
                            <table class="mt-2 w-full text-center text-xs">
                                <thead><tr class="text-gray-500"><th class="text-left">Eye</th><th>SPH</th><th>CYL</th><th>AXIS</th><th>ADD</th></tr></thead>
                                <tbody>
                                <tr><td class="text-left font-medium">OD</td><td>{{ $rx->od_sphere ?? '—' }}</td><td>{{ $rx->od_cylinder ?? '—' }}</td><td>{{ $rx->od_axis ?? '—' }}</td><td>{{ $rx->od_add ?? '—' }}</td></tr>
                                <tr><td class="text-left font-medium">OS</td><td>{{ $rx->os_sphere ?? '—' }}</td><td>{{ $rx->os_cylinder ?? '—' }}</td><td>{{ $rx->os_axis ?? '—' }}</td><td>{{ $rx->os_add ?? '—' }}</td></tr>
                                </tbody>
                            </table>
                            @if($rx->notes)<p class="mt-2 text-gray-600">{{ $rx->notes }}</p>@endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No prescriptions recorded yet.</p>
                    @endforelse
                </div>
            </div></div>

            {{-- Bookings --}}
            <div class="card"><div class="card-body">
                <h2 class="card-title">Bookings &amp; visits</h2>
                <ul class="mt-3 divide-y divide-gray-100 text-sm">
                    @forelse($patient->bookings as $b)
                        <li class="py-2 flex flex-wrap items-center gap-3"><a href="{{ route('admin.bookings.show', $b) }}" class="font-mono text-xs text-brand hover:underline">{{ $b->reference }}</a><span>{{ $b->service }}</span><x-badge :status="$b->status" /><span class="ml-auto text-gray-500">{{ $b->appointment_at?->format('j M Y, g:ia') ?? $b->created_at->format('j M Y') }}</span></li>
                    @empty
                        <li class="py-2 text-gray-500">No bookings linked.</li>
                    @endforelse
                </ul>
            </div></div>
        </div>

        <div class="space-y-6">
            {{-- Notes timeline --}}
            <div class="card"><div class="card-body">
                <h2 class="card-title">Notes</h2>
                <form method="POST" action="{{ route('admin.patients.notes.store', $patient) }}" class="mt-3 space-y-2" data-once>
                    @csrf
                    <x-form.textarea name="body" rows="3" placeholder="Call summary, follow-up, frame chosen…" />
                    <button class="btn-primary btn-sm">Add note</button>
                </form>
                <ul class="mt-4 space-y-3 text-sm">
                    @forelse($patient->notes as $note)
                        <li class="rounded-lg bg-gray-50 p-3">
                            <div class="whitespace-pre-line">{{ $note->body }}</div>
                            <div class="mt-2 flex items-center gap-2 text-xs text-gray-500"><span>{{ $note->author?->name ?? 'System' }}</span>·<span>{{ $note->created_at->format('j M Y, g:ia') }}</span>
                                @if(auth()->user()->isAdmin() || $note->user_id === auth()->id())<form method="POST" action="{{ route('admin.patients.notes.destroy', [$patient, $note]) }}" class="ml-auto" data-confirm="Remove this note?">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Remove</button></form>@endif
                            </div>
                        </li>
                    @empty
                        <li class="text-gray-500">No notes yet.</li>
                    @endforelse
                </ul>
            </div></div>

            <div class="card"><div class="card-body">
                <h2 class="card-title">Record history</h2>
                <ul class="mt-3 divide-y divide-gray-100 text-xs">
                    @forelse($history as $log)
                        <li class="py-2"><span class="text-gray-400">{{ $log->created_at->format('j M, g:ia') }}</span> <strong>{{ $log->user?->name ?? 'System' }}</strong> {{ $log->description }}</li>
                    @empty
                        <li class="py-2 text-gray-500">—</li>
                    @endforelse
                </ul>
            </div></div>

            @can('delete', $patient)
                <form method="POST" action="{{ route('admin.patients.destroy', $patient) }}" data-confirm="Archive this patient? Their record is kept for audit but hidden from lists.">
                    @csrf @method('DELETE')
                    <button class="btn-danger btn-sm w-full">Archive patient</button>
                </form>
            @endcan
        </div>
    </div>
</x-admin-layout>
