<?php

namespace App\Filament\Resources\LearningMaterials\Schemas;

use App\Models\Course;
use App\Models\LearningMaterial;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class LearningMaterialForm
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
                        Select::make('type')->label('Jenis')->options(LearningMaterial::TYPES)->required()->live()->default('pdf'),
                        TextInput::make('title')->label('Judul')->required()->maxLength(255),
                        TextInput::make('sort')->label('Urutan')->numeric()->default(0),
                        Textarea::make('description')->label('Deskripsi')->rows(3)->columnSpanFull(),
                    ]),
                Section::make('File materi')
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => $get('type') === 'pdf')
                    ->schema([
                        FileUpload::make('file_path')->label('File (PDF, Word, PowerPoint, gambar)')
                            ->disk('public')->directory('materials')
                            ->storeFileNamesIn('file_name')
                            ->acceptedFileTypes([
                                'application/pdf', 'image/*', 'application/msword', 'application/vnd.ms-powerpoint',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            ])
                            ->maxSize(51200)->downloadable()->openable(),
                    ]),
                Section::make('Video')
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => $get('type') === 'video')
                    ->columns(2)
                    ->schema([
                        TextInput::make('url')->label('Link video (YouTube / Google Drive)')->url()->required(),
                        TextInput::make('duration_minutes')->label('Durasi (menit)')->numeric()->minValue(1),
                    ]),
                Section::make('Soal kuis')
                    ->columnSpanFull()
                    ->description('Pilihan ganda. Nilai kuis otomatis masuk ke rekap nilai warga belajar.')
                    ->visible(fn (Get $get) => $get('type') === 'quiz')
                    ->schema([
                        Repeater::make('questions')->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort')
                            ->addActionLabel('Tambah soal')
                            ->itemLabel(fn (array $state) => str($state['question'] ?? 'Soal baru')->limit(60))
                            ->collapsible()
                            ->columns(2)
                            ->schema([
                                Textarea::make('question')->label('Pertanyaan')->required()->rows(2)->columnSpanFull(),
                                Repeater::make('options')->label('Pilihan jawaban (urut A, B, C, ...)')
                                    ->simple(TextInput::make('text')->required())
                                    ->minItems(2)->maxItems(5)->defaultItems(4)->reorderable(false)
                                    ->addActionLabel('Tambah pilihan'),
                                Select::make('answer')->label('Jawaban benar')->required()
                                    ->options(['A', 'B', 'C', 'D', 'E']),
                            ]),
                    ]),
            ]);
    }
}
