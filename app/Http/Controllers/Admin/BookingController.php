<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingRequest;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Booking::class);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(Booking::STATUSES)],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $bookings = Booking::with(['patient', 'assignee'])
            ->search($filters['q'] ?? null)
            ->status($filters['status'] ?? null)
            ->when($filters['from'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($filters['to'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = Booking::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.bookings.index', compact('bookings', 'filters', 'counts'));
    }

    public function create(): View
    {
        $this->authorize('create', Booking::class);

        return view('admin.bookings.form', [
            'booking' => new Booking(['status' => 'new', 'source' => 'phone']),
            'staff' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        $this->authorize('create', Booking::class);

        $booking = Booking::create($request->bookingData());

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking added.');
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        return view('admin.bookings.show', [
            'booking' => $booking->load(['patient', 'assignee']),
            'matches' => $booking->isConverted() ? collect() : $booking->matchingPatients(),
            'history' => ActivityLog::where('subject_type', Booking::class)->where('subject_id', $booking->id)
                ->with('user')->latest('created_at')->limit(20)->get(),
        ]);
    }

    public function edit(Booking $booking): View
    {
        $this->authorize('update', $booking);

        return view('admin.bookings.form', [
            'booking' => $booking,
            'staff' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(BookingRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);

        $booking->update($request->bookingData());

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking updated.');
    }

    /**
     * Turn a lead into a patient record: either link it to an existing patient
     * (matched by phone/email) or create a brand-new one from the booking details.
     */
    public function convert(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('convert', $booking);

        $data = $request->validate([
            'patient_id' => ['nullable', 'integer', Rule::exists(Patient::class, 'id')],
        ]);

        $patient = DB::transaction(function () use ($booking, $data) {
            if (! empty($data['patient_id'])) {
                $patient = Patient::findOrFail($data['patient_id']);
            } else {
                [$first, $last] = array_pad(explode(' ', trim($booking->name), 2), 2, '');
                $patient = Patient::create([
                    'first_name' => $first ?: $booking->name,
                    'last_name' => $last,
                    'phone' => $booking->phone,
                    'email' => $booking->email,
                ]);
            }

            $booking->update([
                'patient_id' => $patient->id,
                'status' => in_array($booking->status, ['new', 'contacted'], true) ? 'scheduled' : $booking->status,
            ]);

            $patient->notes()->create([
                'body' => "Created from website booking {$booking->reference} ({$booking->service})."
                    .($booking->notes ? "\nCustomer message: {$booking->notes}" : ''),
            ]);

            ActivityLog::record('converted', $booking, description: "Booking {$booking->reference} converted to patient {$patient->patient_number}");

            return $patient;
        });

        return redirect()->route('admin.patients.show', $patient)
            ->with('success', "Booking converted. Patient {$patient->patient_number} is ready to complete.");
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $this->authorize('delete', $booking);

        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted.');
    }
}
