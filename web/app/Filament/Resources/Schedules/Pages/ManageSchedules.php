<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use App\Models\Schedule;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Carbon;

class ManageSchedules extends ManageRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->using(function (array $data) {
                $weeks = max(1, (int) ($data['repeat_weeks'] ?? 1));
                unset($data['repeat_weeks']);
                $first = null;
                for ($i = 0; $i < $weeks; $i++) {
                    $record = Schedule::create([...$data, 'date' => Carbon::parse($data['date'])->addWeeks($i)]);
                    $first ??= $record;
                }

                return $first;
            }),
        ];
    }
}
