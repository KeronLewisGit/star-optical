@php $editing = $booking->exists; @endphp
<x-admin-layout :title="$editing ? 'Edit booking '.$booking->reference : 'Add booking'">
    <form method="POST" action="{{ $editing ? route('admin.bookings.update', $booking) : route('admin.bookings.store') }}" class="grid lg:grid-cols-3 gap-6" data-once>
        @csrf
        @if($editing)@method('PUT')@endif

        <div class="card lg:col-span-2">
            <div class="card-body space-y-4">
                <h2 class="card-title">Customer details</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <x-form.input name="name" label="Name" :value="$booking->name" required />
                    <x-form.input name="phone" label="Phone" type="tel" :value="$booking->formatted_phone" required />
                    <x-form.input name="email" label="Email" type="email" :value="$booking->email" class="sm:col-span-2" />
                </div>
                <div class="grid sm:grid-cols-3 gap-4">
                    <x-form.select name="service" label="Service" :options="\App\Models\Booking::SERVICES" :value="$booking->service" required />
                    <x-form.input name="preferred_date" label="Preferred day" type="date" :value="$booking->preferred_date?->format('Y-m-d')" />
                    <x-form.select name="preferred_time" label="Preferred time" :options="\App\Models\Booking::TIMES" :value="$booking->preferred_time" placeholder="—" />
                </div>
                <x-form.textarea name="notes" label="Customer message" :value="$booking->notes" rows="3" />
            </div>
        </div>

        <div class="space-y-6">
            <div class="card"><div class="card-body space-y-4">
                <h2 class="card-title">Handling</h2>
                <x-form.select name="status" label="Status" :options="\App\Models\Booking::STATUSES" :value="$booking->status" required />
                <x-form.select name="source" label="Source" :options="\App\Models\Booking::SOURCES" :value="$booking->source" required />
                <x-form.input name="appointment_at" label="Appointment date and time" type="datetime-local" :value="$booking->appointment_at?->format('Y-m-d\TH:i')" help="Set when the status is Scheduled." />
                <x-form.select name="assigned_to" label="Assigned to" :options="$staff->pluck('name', 'id')->all()" :value="$booking->assigned_to" placeholder="Unassigned" />
                <x-form.textarea name="internal_notes" label="Internal notes" :value="$booking->internal_notes" rows="4" help="Staff only. Encrypted at rest." />
            </div></div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">{{ $editing ? 'Save changes' : 'Add booking' }}</button>
                <a href="{{ $editing ? route('admin.bookings.show', $booking) : route('admin.bookings.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</x-admin-layout>
