<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Models\Student;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

/** Field name, username, email, password disimpan ke tabel users (lihat CreateStudent/EditStudent). */
class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data warga belajar')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Nama lengkap')->required()->maxLength(255),
                        TextInput::make('nisn')->label('NISN')->required()->maxLength(20)->unique(ignoreRecord: true),
                        Select::make('paket')->options(Student::PAKET)->required()->live()
                            ->afterStateUpdated(fn (Set $set) => $set('kelas', null)),
                        Select::make('kelas')->required()
                            ->options(fn (Get $get) => Student::kelasOptions($get('paket'))),
                        TextInput::make('phone')->label('No. HP')->tel()->maxLength(30),
                        Toggle::make('active')->label('Aktif')->default(true)->inline(false),
                        Textarea::make('address')->label('Alamat')->rows(2)->columnSpanFull(),
                    ]),
                Section::make('Akun login aplikasi')
                    ->columnSpanFull()
                    ->description('Warga belajar bisa login pakai NISN atau username.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('username')->maxLength(50)->alphaDash()
                            ->unique('users', 'username', ignorable: fn (?Student $record) => $record?->user),
                        TextInput::make('email')->email()->maxLength(255)
                            ->unique('users', 'email', ignorable: fn (?Student $record) => $record?->user),
                        TextInput::make('password')->password()->revealable()->minLength(6)
                            ->required(fn (string $operation) => $operation === 'create')
                            ->helperText(fn (string $operation) => $operation === 'edit' ? 'Kosongkan kalau tidak diganti.' : null),
                    ]),
            ]);
    }
}
