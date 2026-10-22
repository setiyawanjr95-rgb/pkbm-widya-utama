<?php

namespace App\Filament\Resources\LearningMaterials\Tables;

use App\Models\LearningMaterial;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LearningMaterialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('course'))
            ->defaultSort('course_id')
            ->defaultGroup('course.name')
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable()->wrap(),
                TextColumn::make('course.name')->label('Mapel')->description(fn (LearningMaterial $r) => "{$r->course->paket} · {$r->course->kelas}"),
                TextColumn::make('type')->label('Jenis')->badge()
                    ->formatStateUsing(fn (string $state) => LearningMaterial::TYPES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) { 'video' => 'danger', 'quiz' => 'success', default => 'info' }),
                TextColumn::make('sort')->label('Urutan')->sortable(),
                TextColumn::make('created_at')->label('Dibuat')->date('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('course')->label('Mapel')->relationship('course', 'name')->searchable()->preload(),
                SelectFilter::make('type')->label('Jenis')->options(LearningMaterial::TYPES),
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
