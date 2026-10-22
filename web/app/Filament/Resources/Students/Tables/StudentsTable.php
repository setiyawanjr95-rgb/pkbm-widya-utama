<?php

namespace App\Filament\Resources\Students\Tables;

use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('user'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('nisn')->label('NISN')->searchable()->copyable(),
                TextColumn::make('user.username')->label('Username')->searchable()->toggleable(),
                TextColumn::make('paket')->badge()->sortable(),
                TextColumn::make('kelas')->sortable(),
                TextColumn::make('phone')->label('No. HP')->toggleable(),
                IconColumn::make('active')->label('Aktif')->boolean(),
                TextColumn::make('created_at')->label('Terdaftar')->date('d M Y')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('paket')->options(Student::PAKET),
                SelectFilter::make('kelas')->options(Student::kelasOptions()),
                TernaryFilter::make('active')->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('resetPassword')
                    ->label('Reset password')
                    ->icon(Heroicon::OutlinedKey)
                    ->color('warning')
                    ->schema([TextInput::make('password')->label('Password baru')->password()->revealable()->required()->minLength(6)])
                    ->action(function (Student $record, array $data) {
                        $record->user->update(['password' => $data['password']]);
                        $record->user->tokens()->delete();
                        Notification::make()->title('Password berhasil direset')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->after(fn (Collection $records) => $records->each(fn ($r) => $r->user()->delete())),
                ]),
            ]);
    }
}
