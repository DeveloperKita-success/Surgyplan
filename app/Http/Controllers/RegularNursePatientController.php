<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegularNursePatientController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isRegularNurse(), 403);

        return view('nurse-regular.patients.index', [
            'patients' => $request->user()
                ->createdPatients()
                ->latest()
                ->paginate(10),
        ]);
    }
}
