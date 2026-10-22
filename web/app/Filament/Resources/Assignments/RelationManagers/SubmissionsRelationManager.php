<?php

namespace App\Filament\Resources\Assignments\RelationManagers;

use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $title = 'Pengumpulan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('score')->label('Nilai (0-100)')->numeric()->minValue(0)->maxValue(100)->required(),
                Textarea::make('feedback')->label('Catatan untuk warga belajar')->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('student.user'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('student.user.name')->label('Warga belajar')->searchable()
                    ->description(fn (Submission $r) => 'NISN '.$r->student->nisn),
                TextColumn::make('file_name')->label('File')->limit(30),
                TextColumn::make('updated_at')->label('Dikirim')->dateTime('d M Y H:i')
                    ->color(fn (Submission $r) => $r->updated_at->gt($this->getOwnerRecord()->deadline) ? 'danger' : null)
                    ->description(fn (Submission $r) => $r->updated_at->gt($this->getOwnerRecord()->deadline) ? 'Terlambat' : null),
                TextColumn::make('score')->label('Nilai')->badge()->placeholder('Belum dinilai')
                    ->color(fn (?int $state) => $state === null ? 'warning' : ($state >= 70 ? 'success' : 'danger')),
            ])
            ->filters([
                TernaryFilter::make('graded')->label('Sudah dinilai')
                    ->queries(true: fn ($q) => $q->whereNotNull('score'), false: fn ($q) => $q->whereNull('score')),
            ])
            ->recordActions([
                Action::make('download')->label('Unduh')->icon(Heroicon::OutlinedArrowDownTray)
                    ->url(fn (Submission $r) => route('submissions.download', $r), shouldOpenInNewTab: true),
                EditAction::make()->label('Nilai')->icon(Heroicon::OutlinedPencilSquare)
                    ->modalHeading(fn (Submission $r) => 'Nilai tugas '.$r->student->user->name)
                    ->mutateDataUsing(fn (array $data) => [...$data, 'graded_at' => now()]),
            ]);
    }
}
