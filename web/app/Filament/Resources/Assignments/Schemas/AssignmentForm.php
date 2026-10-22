<?php

namespace App\Filament\Resources\Assignments\Schemas;

use App\Models\Course;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('course_id')->label('Mata pelajaran')->required()
                            ->relationship('course', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Course $record) => $record->label)
                            ->searchable()->preload(),
                        DateTimePicker::make('deadline')->label('Batas pengumpulan')->required()->seconds(false)
                            ->default(now()->addWeek()->setTime(23, 59)),
                        TextInput::make('title')->label('Judul tugas')->required()->maxLength(255)->columnSpanFull(),
                        Textarea::make('instruction')->label('Instruksi')->rows(4)->columnSpanFull(),
                        FileUpload::make('file_path')->label('Lampiran soal (opsional)')
                            ->disk('public')->directory('assignments')
                            ->storeFileNamesIn('file_name')
                            ->maxSize(51200)->downloadable()->openable()->columnSpanFull(),
                    ]),
            ]);
    }
}
