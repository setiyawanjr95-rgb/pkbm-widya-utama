<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\Course;
use App\Models\Student;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Nama mapel')->required()->maxLength(255),
                        TextInput::make('tutor')->label('Tutor')->maxLength(255),
                        Select::make('paket')->options(Student::PAKET)->required()->live()
                            ->afterStateUpdated(fn (Set $set) => $set('kelas', null)),
                        Select::make('kelas')->required()
                            ->options(fn (Get $get) => Student::kelasOptions($get('paket')))
                            ->helperText('Mapel otomatis muncul di app untuk warga belajar paket & kelas ini.'),
                        Select::make('icon')->label('Ikon di app')->options(Course::ICONS)->default('book')->required(),
                        Select::make('color')->label('Warna di app')->options(Course::COLORS)->default('primary')->required(),
                        Textarea::make('description')->label('Deskripsi')->rows(3)->columnSpanFull(),
                        Toggle::make('finished')->label('Sudah selesai')
                            ->helperText('Mapel selesai pindah ke tab "Selesai" di app.'),
                    ]),
            ]);
    }
}
