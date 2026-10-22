<?php

namespace App\Filament\Resources\Staff;

use App\Filament\Resources\Staff\Pages\ManageStaff;
use App\Models\Staff;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Pengelola & tutor yang tampil di halaman Profil. */
class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $modelLabel = 'pengelola/tutor';

    protected static ?string $pluralModelLabel = 'Pengelola & Tutor';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->label('Nama')->required()->maxLength(255),
                TextInput::make('position')->label('Jabatan')->required()->placeholder('Tutor Matematika'),
                FileUpload::make('photo')->label('Foto')->image()->disk('public')->directory('staff')->imageEditor()->imageAspectRatio('1:1'),
                TextInput::make('sort')->label('Urutan')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('photo')->label('')->disk('public')->circular(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('position')->label('Jabatan'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageStaff::route('/')];
    }
}
