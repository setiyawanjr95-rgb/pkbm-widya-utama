<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Models\Student;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount(['materials', 'assignments']))
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->label('Mapel')->searchable()->sortable(),
                TextColumn::make('paket')->badge()->sortable(),
                TextColumn::make('kelas')->sortable(),
                TextColumn::make('tutor')->searchable(),
                TextColumn::make('materials_count')->label('Materi')->numeric(),
                TextColumn::make('assignments_count')->label('Tugas')->numeric(),
                IconColumn::make('finished')->label('Selesai')->boolean(),
            ])
            ->filters([
                SelectFilter::make('paket')->options(Student::PAKET),
                SelectFilter::make('kelas')->options(Student::kelasOptions()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
