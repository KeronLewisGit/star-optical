@php $editing = $patient->exists; @endphp
<x-admin-layout :title="$editing ? 'Edit '.$patient->full_name : 'New patient'">
    <form method="POST" action="{{ $editing ? route('admin.patients.update', $patient) : route('admin.patients.store') }}" class="grid lg:grid-cols-3 gap-6" data-once>
        @csrf
        @if($editing)@method('PUT')@endif

        <div class="lg:col-span-2 space-y-6">
            <div class="card"><div class="card-body space-y-4">
                <h2 class="card-title">Personal details</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <x-form.input name="first_name" label="First name" :value="$patient->first_name" required />
                    <x-form.input name="last_name" label="Last name" :value="$patient->last_name" />
                    <x-form.input name="phone" label="Phone" type="tel" :value="$patient->phone ? $patient->formatted_phone : ''" required />
                    <x-form.input name="phone_alt" label="Alternate phone" type="tel" :value="$patient->phone_alt" />
                    <x-form.input name="email" label="Email" type="email" :value="$patient->email" />
                    <x-form.input name="date_of_birth" label="Date of birth" type="date" :value="$patient->date_of_birth?->format('Y-m-d')" />
                    <x-form.select name="gender" label="Gender" :options="array_combine(\App\Models\Patient::GENDERS, array_map(fn ($g) => ucfirst(str_replace('_', ' ', $g)), \App\Models\Patient::GENDERS))" :value="$patient->gender" placeholder="—" />
                    <x-form.input name="insurance_provider" label="Insurance provider" :value="$patient->insurance_provider" />
                    <x-form.input name="address" label="Address" :value="$patient->address" class="sm:col-span-2" />
                </div>
            </div></div>

            <div class="card"><div class="card-body space-y-4">
                <h2 class="card-title">Emergency contact</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <x-form.input name="emergency_contact_name" label="Name" :value="$patient->emergency_contact_name" />
                    <x-form.input name="emergency_contact_phone" label="Phone" type="tel" :value="$patient->emergency_contact_phone" />
                </div>
            </div></div>

            <div class="card"><div class="card-body space-y-4">
                <h2 class="card-title">Medical <span class="ml-2 badge bg-gray-100 text-gray-600"><i class="fa-solid fa-lock mr-1"></i> encrypted</span></h2>
                <x-form.textarea name="allergies" label="Allergies" :value="$patient->allergies" rows="2" />
                <x-form.textarea name="medical_notes" label="Medical notes" :value="$patient->medical_notes" rows="4" help="Conditions, medication, family history, previous surgery." />
            </div></div>
        </div>

        <div class="space-y-6">
            <div class="card"><div class="card-body space-y-4">
                <h2 class="card-title">Record</h2>
                @if($editing)<p class="text-sm text-gray-500">Patient number <span class="font-mono">{{ $patient->patient_number }}</span></p>@endif
                <x-form.select name="status" label="Status" :options="\App\Models\Patient::STATUSES" :value="$patient->status" required />
            </div></div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">{{ $editing ? 'Save changes' : 'Create patient' }}</button>
                <a href="{{ $editing ? route('admin.patients.show', $patient) : route('admin.patients.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</x-admin-layout>
