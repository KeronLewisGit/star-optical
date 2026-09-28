<x-admin-layout :title="'Booking '.$booking->reference">
    <div class="flex flex-wrap items-center gap-3">
        <x-badge :status="$booking->status" class="text-sm" />
        <span class="text-sm text-gray-500">Received {{ $booking->created_at->format('D j M Y, g:ia') }} via {{ $booking->source }}</span>
        <div class="ml-auto flex gap-2">
            <a href="{{ \App\Models\Setting::whatsappLink('') }}" target="_blank" rel="noopener" class="btn-secondary btn-sm"><i class="fa-brands fa-whatsapp text-wa"></i> WhatsApp</a>
            <a href="tel:{{ $booking->phone }}" class="btn-secondary btn-sm"><i class="fa-solid fa-phone"></i> Call</a>
            <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn-primary btn-sm"><i class="fa-solid fa-pen"></i> Edit</a>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card"><div class="card-body">
                <h2 class="card-title">Customer</h2>
                <dl class="mt-3 grid sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-gray-500">Name</dt><dd class="font-medium">{{ $booking->name }}</dd></div>
                    <div><dt class="text-gray-500">Phone</dt><dd class="font-medium">{{ $booking->formatted_phone }}</dd></div>
                    <div><dt class="text-gray-500">Email</dt><dd class="font-medium">{{ $booking->email ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Service</dt><dd class="font-medium">{{ $booking->service }}</dd></div>
                    <div><dt class="text-gray-500">Preferred</dt><dd class="font-medium">{{ $booking->preferred_date?->format('l j F Y') ?? '—' }} {{ $booking->preferred_time }}</dd></div>
                    <div><dt class="text-gray-500">Appointment</dt><dd class="font-medium">{{ $booking->appointment_at?->format('l j F Y, g:ia') ?? 'Not scheduled' }}</dd></div>
                    <div><dt class="text-gray-500">Assigned to</dt><dd class="font-medium">{{ $booking->assignee?->name ?? 'Unassigned' }}</dd></div>
                    <div><dt class="text-gray-500">Contacted</dt><dd class="font-medium">{{ $booking->contacted_at?->diffForHumans() ?? 'Not yet' }}</dd></div>
                </dl>
                @if($booking->notes)<div class="mt-4 rounded-lg bg-gray-50 p-3 text-sm"><p class="text-xs uppercase text-gray-500 mb-1">Customer message</p>{{ $booking->notes }}</div>@endif
                @if($booking->internal_notes)<div class="mt-3 rounded-lg bg-amber-50 p-3 text-sm"><p class="text-xs uppercase text-amber-700 mb-1">Internal notes</p><div class="whitespace-pre-line">{{ $booking->internal_notes }}</div></div>@endif
            </div></div>

            <div class="card"><div class="card-body">
                <h2 class="card-title">History</h2>
                <ul class="mt-3 divide-y divide-gray-100 text-sm">
                    @forelse($history as $log)
                        <li class="py-2 flex gap-3"><span class="text-gray-400 whitespace-nowrap">{{ $log->created_at->format('j M, g:ia') }}</span><span><strong>{{ $log->user?->name ?? 'Website' }}</strong> {{ $log->description }}
                            @if($log->changes)<span class="text-gray-500">({{ collect($log->changes)->map(fn ($c, $k) => "$k: ".($c['from'] ?? '—')." → ".($c['to'] ?? '—'))->join(', ') }})</span>@endif</span></li>
                    @empty
                        <li class="py-2 text-gray-500">No history recorded.</li>
                    @endforelse
                </ul>
            </div></div>
        </div>

        <div class="space-y-6">
            @if($booking->isConverted())
                <div class="card border-green-200"><div class="card-body">
                    <h2 class="card-title text-green-800"><i class="fa-solid fa-circle-check"></i> Converted to patient</h2>
                    <p class="mt-2 text-sm">{{ $booking->patient->full_name }}<br><span class="text-gray-500">{{ $booking->patient->patient_number }}</span></p>
                    <a href="{{ route('admin.patients.show', $booking->patient) }}" class="btn-primary btn-sm mt-3">Open patient record</a>
                </div></div>
            @else
                <div class="card border-gold"><div class="card-body">
                    <h2 class="card-title">Convert to patient</h2>
                    <p class="mt-1 text-sm text-gray-600">Create a patient record from this lead so you can keep their prescription, notes and visit history.</p>
                    @if($matches->isNotEmpty())
                        <p class="mt-3 text-xs font-semibold uppercase text-amber-700">Possible existing patient</p>
                        @foreach($matches as $m)
                            <form method="POST" action="{{ route('admin.bookings.convert', $booking) }}" class="mt-2 flex items-center justify-between gap-2 rounded-lg border border-amber-200 bg-amber-50 p-2 text-sm" data-once>
                                @csrf <input type="hidden" name="patient_id" value="{{ $m->id }}">
                                <span>{{ $m->full_name }}<br><span class="text-xs text-gray-500">{{ $m->patient_number }} · {{ $m->formatted_phone }}</span></span>
                                <button class="btn-secondary btn-sm">Link</button>
                            </form>
                        @endforeach
                    @endif
                    <form method="POST" action="{{ route('admin.bookings.convert', $booking) }}" class="mt-3" data-once>
                        @csrf
                        <button class="btn-gold w-full"><i class="fa-solid fa-user-plus"></i> Create new patient</button>
                    </form>
                </div></div>
            @endif

            <div class="card"><div class="card-body space-y-3">
                <h2 class="card-title">Quick status</h2>
                <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="space-y-3" data-once>
                    @csrf @method('PUT')
                    @foreach(['name', 'phone', 'email', 'service', 'preferred_date', 'preferred_time', 'notes', 'source', 'internal_notes', 'assigned_to'] as $f)
                        <input type="hidden" name="{{ $f }}" value="{{ $f === 'preferred_date' ? $booking->preferred_date?->format('Y-m-d') : $booking->$f }}">
                    @endforeach
                    <x-form.select name="status" :options="\App\Models\Booking::STATUSES" :value="$booking->status" />
                    <x-form.input name="appointment_at" type="datetime-local" :value="$booking->appointment_at?->format('Y-m-d\TH:i')" help="Appointment date and time" />
                    <button class="btn-secondary w-full">Update status</button>
                </form>
            </div></div>

            @can('delete', $booking)
                <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" data-confirm="Delete this booking? This cannot be undone from the admin.">
                    @csrf @method('DELETE')
                    <button class="btn-danger btn-sm w-full">Delete booking</button>
                </form>
            @endcan
        </div>
    </div>
</x-admin-layout>
