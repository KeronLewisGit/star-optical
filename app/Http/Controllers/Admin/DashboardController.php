<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Patient;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $startOfMonth = now()->startOfMonth();

        return view('admin.dashboard', [
            'newBookings' => Booking::where('status', 'new')->count(),
            'bookingsThisMonth' => Booking::where('created_at', '>=', $startOfMonth)->count(),
            'scheduledToday' => Booking::where('status', 'scheduled')->whereDate('appointment_at', today())->count(),
            'patientsTotal' => Patient::count(),
            'patientsThisMonth' => Patient::where('created_at', '>=', $startOfMonth)->count(),
            'conversionRate' => $this->conversionRate(),
            'recentBookings' => Booking::with('patient')->latest()->limit(8)->get(),
            'upcoming' => Booking::with('patient')->where('status', 'scheduled')
                ->where('appointment_at', '>=', now())->orderBy('appointment_at')->limit(6)->get(),
            'byService' => Booking::selectRaw('service, count(*) as total')->groupBy('service')->orderByDesc('total')->get(),
        ]);
    }

    private function conversionRate(): int
    {
        $total = Booking::count();

        return $total === 0 ? 0 : (int) round(Booking::whereNotNull('patient_id')->count() / $total * 100);
    }
}
