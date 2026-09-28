<x-admin-layout title="Patients">
    <form method="GET" class="card card-body flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[220px]"><label class="form-label">Search</label><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" class="form-input" placeholder="Name, phone, email or patient number"></div>
        <div><label class="form-label">Status</label><select name="status" class="form-input"><option value="">All</option>@foreach(\App\Models\Patient::STATUSES as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ ucfirst($s) }}</option>@endforeach</select></div>
        <button class="btn-secondary">Search</button>
        <a href="{{ route('admin.patients.create') }}" class="btn-primary ml-auto"><i class="fa-solid fa-user-plus"></i> New patient</a>
    </form>

    <div class="card overflow-x-auto">
        <table class="table">
            <thead><tr><th>Patient #</th><th>Name</th><th>Phone</th><th>Email</th><th>DOB</th><th>Visits</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($patients as $p)
                <tr>
                    <td class="font-mono text-xs text-gray-500">{{ $p->patient_number }}</td>
                    <td><a href="{{ route('admin.patients.show', $p) }}" class="font-medium text-gray-900 hover:text-brand">{{ $p->full_name }}</a></td>
                    <td>{{ $p->formatted_phone }}</td>
                    <td class="text-gray-500">{{ $p->email ?? '—' }}</td>
                    <td class="text-gray-500 whitespace-nowrap">{{ $p->date_of_birth?->format('j M Y') ?? '—' }}</td>
                    <td class="text-gray-500">{{ $p->bookings_count }} bookings · {{ $p->prescriptions_count }} Rx</td>
                    <td><x-badge :status="$p->status" /></td>
                    <td class="text-right"><a href="{{ route('admin.patients.show', $p) }}" class="btn-secondary btn-sm">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-gray-500 py-10">No patients found. Convert a booking or add a patient manually.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $patients->links() }}
</x-admin-layout>
