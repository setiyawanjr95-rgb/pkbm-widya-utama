<?php

namespace App\Filament\Widgets;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Submission;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        $students = Student::where('active', true);

        return [
            Stat::make('Warga belajar aktif', $students->count())
                ->description(collect(Student::PAKET)->keys()
                    ->map(fn ($p) => $p.': '.Student::where('active', true)->where('paket', $p)->count())->join(' · '))
                ->icon(Heroicon::OutlinedUserGroup),
            Stat::make('Mata pelajaran', Course::where('finished', false)->count())
                ->description('Tugas aktif: '.Assignment::where('deadline', '>=', now())->count())
                ->icon(Heroicon::OutlinedBookOpen),
            Stat::make('Menunggu dinilai', Submission::whereNull('score')->count())
                ->description('Pengumpulan tugas yang belum diberi nilai')
                ->color('warning')
                ->icon(Heroicon::OutlinedClipboardDocumentCheck),
            Stat::make('Jadwal hari ini', Schedule::whereDate('date', today())->count())
                ->description(today()->translatedFormat('l, d F Y'))
                ->icon(Heroicon::OutlinedCalendarDays),
        ];
    }
}
