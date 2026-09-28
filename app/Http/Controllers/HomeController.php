<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Promotion;
use App\Models\Setting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('public.home', [
            'settings' => Setting::all_cached(),
            'promotions' => Promotion::live()->get(),
            'services' => Booking::SERVICES,
            'times' => Booking::TIMES,
        ]);
    }
}
