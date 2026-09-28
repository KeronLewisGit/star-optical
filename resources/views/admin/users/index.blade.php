<x-admin-layout title="Staff accounts">
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-600">Administrators can manage settings, staff and delete records. Staff can work with bookings, patients and promotions.</p>
        <a href="{{ route('admin.users.create') }}" class="btn-primary"><i class="fa-solid fa-user-plus"></i> Add account</a>
    </div>
    <div class="card overflow-x-auto">
        <table class="table">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>2FA</th><th>Last sign-in</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @foreach($users as $u)
                <tr class="{{ $u->is_active ? '' : 'opacity-60' }}">
                    <td class="font-medium text-gray-900">{{ $u->name }}@if($u->is(auth()->user())) <span class="text-xs text-gray-400">(you)</span>@endif</td>
                    <td class="text-gray-500">{{ $u->email }}</td>
                    <td><x-badge :status="$u->role" /></td>
                    <td>@if($u->hasTwoFactorEnabled())<span class="badge bg-green-100 text-green-800">On</span>@else<span class="badge bg-gray-100 text-gray-600">Off</span>@endif</td>
                    <td class="text-gray-500 whitespace-nowrap">{{ $u->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td><x-badge :status="$u->is_active ? 'active' : 'inactive'" /></td>
                    <td class="text-right whitespace-nowrap space-x-1">
                        <a href="{{ route('admin.users.edit', $u) }}" class="btn-secondary btn-sm">Edit</a>
                        @unless($u->is(auth()->user()))
                            <form method="POST" action="{{ route('admin.users.toggle', $u) }}" class="inline" data-confirm="{{ $u->is_active ? 'Deactivate' : 'Reactivate' }} {{ $u->name }}?">@csrf @method('PATCH')<button class="btn-secondary btn-sm">{{ $u->is_active ? 'Deactivate' : 'Reactivate' }}</button></form>
                        @endunless
                        @if($u->hasTwoFactorEnabled())
                            <form method="POST" action="{{ route('admin.users.two-factor.reset', $u) }}" class="inline" data-confirm="Reset two-factor for {{ $u->name }}? They will sign in with password only until they set it up again.">@csrf @method('DELETE')<button class="btn-secondary btn-sm">Reset 2FA</button></form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
