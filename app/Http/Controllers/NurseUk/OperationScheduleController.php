<?php

namespace App\Http\Controllers\NurseUk;

use App\Http\Controllers\Controller;
use App\Models\OperatingRoom;
use App\Models\SurgerySchedule;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperationScheduleController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user?->isUkNurse(), 403);

        $today = CarbonImmutable::today();
        $month = (int) $request->integer('month', $today->month);
        $year = (int) $request->integer('year', $today->year);

        if ($month < 1 || $month > 12) {
            $month = $today->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = $today->year;
        }

        $calendarDate = CarbonImmutable::create($year, $month, 1);
        $selectedDate = $this->selectedDate($request, $calendarDate);
        $startOfMonth = $calendarDate->startOfMonth();
        $endOfMonth = $calendarDate->endOfMonth();

        $monthSchedules = SurgerySchedule::query()
            ->with(['patient', 'doctor.user', 'operatingRoom', 'surgeryRequest'])
            ->whereBetween('surgery_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->whereHas('surgeryRequest', fn ($query) => $query->where('request_status', 'disetujui'))
            ->orderBy('surgery_date')
            ->orderBy('start_time')
            ->get();

        $schedulesByDate = $monthSchedules->groupBy(fn (SurgerySchedule $schedule) => $schedule->surgery_date->toDateString());
        $selectedSchedules = $schedulesByDate->get($selectedDate->toDateString(), collect());
        $bookedRoomIds = $selectedSchedules->pluck('operating_room_id')->unique();

        $rooms = OperatingRoom::query()
            ->with('specialist')
            ->orderBy('room_name')
            ->get();

        return view('nurse-uk.schedules.index', [
            'calendarDate' => $calendarDate,
            'selectedDate' => $selectedDate,
            'month' => $month,
            'year' => $year,
            'years' => range($today->year - 3, $today->year + 3),
            'calendarWeeks' => $this->calendarWeeks($calendarDate, $schedulesByDate),
            'selectedSchedules' => $selectedSchedules,
            'availableRooms' => $rooms
                ->where('status', 'siap')
                ->reject(fn (OperatingRoom $room) => $bookedRoomIds->contains($room->id))
                ->values(),
            'bookedRooms' => $rooms
                ->whereIn('id', $bookedRoomIds)
                ->values(),
            'roomStats' => [
                'total' => $rooms->count(),
                'booked' => $bookedRoomIds->count(),
                'available' => $rooms->where('status', 'siap')->reject(fn (OperatingRoom $room) => $bookedRoomIds->contains($room->id))->count(),
            ],
        ]);
    }

    private function selectedDate(Request $request, CarbonImmutable $calendarDate): CarbonImmutable
    {
        $date = $request->date('date');

        if (! $date) {
            return $calendarDate->isSameMonth(CarbonImmutable::today())
                ? CarbonImmutable::today()
                : $calendarDate;
        }

        $selectedDate = CarbonImmutable::instance($date);

        return $selectedDate->isSameMonth($calendarDate) ? $selectedDate : $calendarDate;
    }

    private function calendarWeeks(CarbonImmutable $calendarDate, $schedulesByDate): array
    {
        $cursor = $calendarDate->startOfMonth()->startOfWeek();
        $end = $calendarDate->endOfMonth()->endOfWeek();
        $weeks = [];

        while ($cursor <= $end) {
            $week = [];

            for ($day = 0; $day < 7; $day++) {
                $dateKey = $cursor->toDateString();
                $schedules = $schedulesByDate->get($dateKey, collect());

                $week[] = [
                    'date' => $cursor,
                    'inMonth' => $cursor->month === $calendarDate->month,
                    'count' => $schedules->count(),
                    'bookedRoomCount' => $schedules->pluck('operating_room_id')->unique()->count(),
                ];

                $cursor = $cursor->addDay();
            }

            $weeks[] = $week;
        }

        return $weeks;
    }
}
