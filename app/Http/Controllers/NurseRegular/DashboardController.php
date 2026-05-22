<?php

namespace App\Http\Controllers\NurseRegular;

use App\Http\Controllers\Controller;
use App\Models\SurgerySchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($user->isRegularNurse(), 403);

        $stats = [
            [
                'label' => 'Total Pengajuan Hari Ini',
                'value' => $this->formatStat(
                    $user->surgeryRequests()
                        ->whereDate('created_at', today())
                        ->count()
                ),
            ],
            [
                'label' => 'Total Pengajuan',
                'value' => $this->formatStat($user->surgeryRequests()->count()),
            ],
            [
                'label' => 'Total Penjadwalan Operasi',
                'value' => $this->formatStat(
                    SurgerySchedule::query()
                        ->whereHas('surgeryRequest', function ($query) use ($user): void {
                            $query->where('requested_by', $user->id);
                        })
                        ->count()
                ),
            ],
        ];

        return view('nurse-regular.dashboard', compact('stats'));
    }

    private function formatStat(int $value): string
    {
        return str_pad((string) $value, 2, '0', STR_PAD_LEFT);
    }
}
