<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->after(fn (Model $record) => $record->user()->delete()),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->record->user;

        return [...$data, 'name' => $user->name, 'username' => $user->username, 'email' => $user->email];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::transaction(function () use ($record, $data) {
            $record->user->update(array_filter([
                'name' => $data['name'],
                'username' => $data['username'] ?: null,
                'email' => $data['email'] ?: null,
                'password' => $data['password'] ?: null,
            ], fn ($v, $k) => $k !== 'password' || $v, ARRAY_FILTER_USE_BOTH));
            $record->update(collect($data)->except(['name', 'username', 'email', 'password'])->all());
        });

        return $record;
    }
}
