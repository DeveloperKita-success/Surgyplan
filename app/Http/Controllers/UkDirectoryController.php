<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\SurgerySchedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UkDirectoryController extends Controller
{
    public function schedules(Request $request): View
    {
        $this->ensureUkNurse($request);

        return view('uk.schedules.index', [
            'schedules' => SurgerySchedule::query()
                ->with(['patient', 'doctor.user', 'operatingRoom'])
                ->latest('surgery_date')
                ->paginate(10),
        ]);
    }

    public function doctors(Request $request): View
    {
        $this->ensureUkNurse($request);

        return view('uk.doctors.index', [
            'doctors' => Doctor::query()
                ->with(['user', 'specialist'])
                ->paginate(10),
        ]);
    }

    private function ensureUkNurse(Request $request): void
    {
        abort_unless($request->user()?->isUkNurse(), 403);
    }
}
