<?php

namespace App\Http\Controllers\NurseOk;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\SurgerySchedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectoryController extends Controller
{
    public function schedules(Request $request): View
    {
        $this->ensureOkNurse($request);

        return view('nurse-ok.schedules.index', [
            'schedules' => SurgerySchedule::query()
                ->with(['patient', 'doctor.user', 'operatingRoom'])
                ->latest('surgery_date')
                ->paginate(10),
        ]);
    }

    public function doctors(Request $request): View
    {
        $this->ensureOkNurse($request);

        return view('nurse-ok.doctors.index', [
            'doctors' => Doctor::query()
                ->with(['user', 'specialist'])
                ->paginate(10),
        ]);
    }

    private function ensureOkNurse(Request $request): void
    {
        abort_unless($request->user()?->isOkNurse(), 403);
    }
}
