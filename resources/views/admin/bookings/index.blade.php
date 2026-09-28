<x-admin-layout title="Bookings">
    <div class="flex flex-wrap items-center gap-2">
        @foreach(\App\Models\Booking::STATUSES as $s)
            <a href="{{ route('admin.bookings.index', array_filter(['status' => $s, 'q' => $filters['q'] ?? null])) }}" class="badge {{ ($filters['status'] ?? '') === $s ? 'ring-2 ring-brand' : '' }} bg-white border border-gray-200 text-gray-700">{{ ucfirst($s) }} <span class="ml-1 text-gray-400">{{ $counts[$s] ?? 0 }}</span></a>
        @endforeach
        <a href="{{ route('admin.bookings.index') }}" class="text-xs text-gray-500 hover:underline">Clear</a>
        <a href="{{ route('admin.bookings.create') }}" class="btn-primary btn-sm ml-auto"><i class="fa-solid fa-plus"></i> Add booking</a>
    </div>

    <form method="GET" class="card card-body grid sm:grid-cols-4 gap-3 items-end">
        <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">
        <div class="sm:col-span-2"><label class="form-label">Search</label><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" class="form-input" placeholder="Name, phone, email or reference"></div>
        <div><label class="form-label">From</label><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-input"></div>
        <div class="flex gap-2"><div class="flex-1"><label class="form-label">To</label><input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-input"></div><button class="btn-secondary self-end">Filter</button></div>
    </form>

    <div class="card overflow-x-auto">
        <table class="table">
            <thead><tr><th>Ref</th><th>Customer</th><th>Service</th><th>Preferred</th><th>Status</th><th>Assigned</th><th>Received</th><th></th></tr></thead>
            <tbody>
            @forelse($bookings as $b)
                <tr>
                    <td class="font-mono text-xs text-gray-500">{{ $b->reference }}</td>
                    <td><a href="{{ route('admin.bookings.show', $b) }}" class="font-medium text-gray-900 hover:text-brand">{{ $b->name }}</a><br><span class="text-xs text-gray-500">{{ $b->formatted_phone }}</span></td>
                    <td>{{ $b->service }}</td>
                    <td class="whitespace-nowrap text-gray-500">{{ $b->preferred_date?->format('D j M') ?? '—' }} {{ $b->preferred_time }}</td>
                    <td><x-badge :status="$b->status" />@if($b->patient)<br><a href="{{ route('admin.patients.show', $b->patient) }}" class="text-xs text-brand hover:underline">{{ $b->patient->patient_number }}</a>@endif</td>
                    <td class="text-gray-500">{{ $b->assignee?->name ?? '—' }}</td>
                    <td class="whitespace-nowrap text-gray-500">{{ $b->created_at->format('j M Y, g:ia') }}</td>
                    <td class="text-right whitespace-nowrap"><a href="{{ route('admin.bookings.show', $b) }}" class="btn-secondary btn-sm">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-gray-500 py-10">No bookings match these filters.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $bookings->links() }}
</x-admin-layout>
