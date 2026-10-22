<?php

namespace App\Filament\Resources\Assignments\Tables;

use App\Models\Assignment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('course')->withCount([
                'submissions',
                'submissions as pending_count' => fn ($q) => $q->whereNull('score'),
            ]))
            ->defaultSort('deadline', 'desc')
            ->columns([
                TextColumn::make('title')->label('Tugas')->searchable()->wrap(),
                TextColumn::make('course.name')->label('Mapel')->description(fn (Assignment $r) => "{$r->course->paket} · {$r->course->kelas}"),
                TextColumn::make('deadline')->label('Batas')->dateTime('d M Y H:i')->sortable()
                    ->color(fn (Assignment $r) => $r->deadline->isPast() ? 'gray' : 'primary'),
                TextColumn::make('submissions_count')->label('Terkumpul')
                    ->formatStateUsing(fn (Assignment $r) => $r->submissions_count.' / '.$r->course->students()->count()),
                TextColumn::make('pending_count')->label('Belum dinilai')->badge()
                    ->color(fn (int $state) => $state ? 'warning' : 'success'),
            ])
            ->filters([
                SelectFilter::make('course')->label('Mapel')->relationship('course', 'name')->searchable()->preload(),
            ])
            ->recordActions([
                EditAction::make()->label('Buka & nilai'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
