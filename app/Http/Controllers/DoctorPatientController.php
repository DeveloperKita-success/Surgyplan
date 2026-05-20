<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorPatientController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isDoctor(), 403);

        return view('doctor.patients.index', [
            'schedules' => $request->user()
                ->doctor
                ->surgerySchedules()
                ->with(['patient', 'surgeryRequest'])
                ->latest('surgery_date')
                ->paginate(10),
        ]);
    }
}
