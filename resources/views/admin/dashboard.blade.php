<x-admin-layout title="Dashboard">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat"><div><p class="stat__label">New bookings</p><p class="stat__value">{{ $newBookings }}</p><a href="{{ route('admin.bookings.index', ['status' => 'new']) }}" class="text-xs text-brand hover:underline">Review leads</a></div><i class="fa-solid fa-inbox text-2xl text-gold"></i></div>
        <div class="stat"><div><p class="stat__label">Bookings this month</p><p class="stat__value">{{ $bookingsThisMonth }}</p></div><i class="fa-solid fa-calendar-check text-2xl text-gold"></i></div>
        <div class="stat"><div><p class="stat__label">Patients</p><p class="stat__value">{{ $patientsTotal }}</p><span class="text-xs text-gray-500">+{{ $patientsThisMonth }} this month</span></div><i class="fa-solid fa-user-group text-2xl text-gold"></i></div>
        <div class="stat"><div><p class="stat__label">Lead → patient</p><p class="stat__value">{{ $conversionRate }}%</p><span class="text-xs text-gray-500">{{ $scheduledToday }} scheduled today</span></div><i class="fa-solid fa-arrow-trend-up text-2xl text-gold"></i></div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="card lg:col-span-2">
            <div class="card-body flex items-center justify-between"><h2 class="card-title">Recent bookings</h2><a href="{{ route('admin.bookings.index') }}" class="text-sm text-brand hover:underline">View all</a></div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Customer</th><th>Service</th><th>Status</th><th>Received</th></tr></thead>
                    <tbody>
                    @forelse($recentBookings as $b)
                        <tr>
                            <td><a href="{{ route('admin.bookings.show', $b) }}" class="font-medium text-gray-900 hover:text-brand">{{ $b->name }}</a><br><span class="text-xs text-gray-500">{{ $b->formatted_phone }}</span></td>
                            <td>{{ $b->service }}</td>
                            <td><x-badge :status="$b->status" /> @if($b->patient)<span class="ml-1 text-xs text-gray-500">→ {{ $b->patient->patient_number }}</span>@endif</td>
                            <td class="whitespace-nowrap text-gray-500">{{ $b->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-gray-500 py-8">No bookings yet. New website bookings will appear here.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card">
                <div class="card-body"><h2 class="card-title">Upcoming appointments</h2>
                    <ul class="mt-3 divide-y divide-gray-100 text-sm">
                        @forelse($upcoming as $b)
                            <li class="py-2 flex justify-between gap-3"><a href="{{ route('admin.bookings.show', $b) }}" class="font-medium hover:text-brand">{{ $b->name }}</a><span class="text-gray-500 whitespace-nowrap">{{ $b->appointment_at->format('D j M, g:ia') }}</span></li>
                        @empty
                            <li class="py-2 text-gray-500">Nothing scheduled.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-body"><h2 class="card-title">Requests by service</h2>
                    <ul class="mt-3 space-y-2 text-sm">
                        @php $max = max(1, $byService->max('total') ?? 1); @endphp
                        @forelse($byService as $row)
                            <li><div class="flex justify-between"><span>{{ $row->service }}</span><span class="text-gray-500">{{ $row->total }}</span></div>
                                <div class="mt-1 h-1.5 rounded bg-gray-100"><div class="h-1.5 rounded bg-brand" style="width: {{ round($row->total / $max * 100) }}%"></div></div></li>
                        @empty
                            <li class="text-gray-500">No data yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
