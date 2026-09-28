<x-admin-layout title="Activity log">
    <form method="GET" class="card card-body grid sm:grid-cols-4 gap-3 items-end">
        <div><label class="form-label">User</label><select name="user_id" class="form-input"><option value="">All</option>@foreach($users as $u)<option value="{{ $u->id }}" @selected(($filters['user_id'] ?? '') == $u->id)>{{ $u->name }}</option>@endforeach</select></div>
        <div><label class="form-label">Action</label><select name="action" class="form-input"><option value="">All</option>@foreach($actions as $a)<option value="{{ $a }}" @selected(($filters['action'] ?? '') === $a)>{{ $a }}</option>@endforeach</select></div>
        <div><label class="form-label">Record type</label><select name="subject" class="form-input"><option value="">All</option>@foreach(['Booking', 'Patient', 'Prescription', 'PatientNote', 'Promotion', 'Setting', 'User'] as $s)<option value="{{ $s }}" @selected(($filters['subject'] ?? '') === $s)>{{ $s }}</option>@endforeach</select></div>
        <div class="flex gap-2"><button class="btn-secondary">Filter</button><a href="{{ route('admin.activity.index') }}" class="btn-secondary">Clear</a></div>
    </form>

    <div class="card overflow-x-auto">
        <table class="table">
            <thead><tr><th>When</th><th>User</th><th>Action</th><th>Record</th><th>Details</th><th>IP</th></tr></thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="whitespace-nowrap text-gray-500">{{ $log->created_at->format('j M Y, g:i:sa') }}</td>
                    <td>{{ $log->user?->name ?? 'Website / system' }}</td>
                    <td><span class="badge bg-gray-100 text-gray-700">{{ $log->action }}</span></td>
                    <td class="text-gray-500">{{ $log->subject_type ? class_basename($log->subject_type).' #'.$log->subject_id : '—' }}</td>
                    <td class="max-w-md">{{ $log->description }}
                        @if($log->changes)<ul class="mt-1 text-xs text-gray-500">@foreach($log->changes as $field => $c)<li><span class="font-medium">{{ $field }}</span>: {{ is_array($c['from'] ?? null) ? json_encode($c['from']) : ($c['from'] ?? '—') }} → {{ is_array($c['to'] ?? null) ? json_encode($c['to']) : ($c['to'] ?? '—') }}</li>@endforeach</ul>@endif
                    </td>
                    <td class="font-mono text-xs text-gray-400">{{ $log->ip_address }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-gray-500 py-10">Nothing logged yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $logs->links() }}
</x-admin-layout>
