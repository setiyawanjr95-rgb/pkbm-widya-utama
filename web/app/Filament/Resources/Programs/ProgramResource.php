<?php

namespace App\Filament\Resources\Programs;

use App\Filament\Resources\Programs\Pages\ManagePrograms;
use App\Models\Program;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $modelLabel = 'program';

    protected static ?string $pluralModelLabel = 'Program';

    protected static ?int $navigationSort = 1;

    public const ICONS = ['paket-a' => 'Pensil (Paket A)', 'paket-b' => 'Buku (Paket B)', 'paket-c' => 'Toga (Paket C)', 'skill' => 'Perkakas (Keterampilan)', 'computer' => 'Komputer', 'book' => 'Buku umum'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->label('Nama program')->required()->maxLength(255),
                TextInput::make('level')->label('Jenjang')->placeholder('Setara SMA'),
                Textarea::make('summary')->label('Ringkasan')->rows(2)->maxLength(500)->columnSpanFull(),
                Repeater::make('features')->label('Poin keunggulan')->simple(TextInput::make('text')->required())
                    ->addActionLabel('Tambah poin')->columnSpanFull(),
                RichEditor::make('body')->label('Penjelasan lengkap')->columnSpanFull()
                    ->toolbarButtons([['bold', 'italic', 'link'], ['h2', 'h3'], ['bulletList', 'orderedList'], ['undo', 'redo']]),
                TextInput::make('duration')->label('Lama belajar')->placeholder('3 tahun / 6 semester'),
                Select::make('icon')->label('Ikon')->options(self::ICONS)->default('book')->required(),
                FileUpload::make('image')->label('Foto program')->image()->disk('public')->directory('programs')->maxSize(4096),
                TextInput::make('sort')->label('Urutan tampil')->numeric()->default(0),
                Toggle::make('open_registration')->label('Bisa dipilih di formulir pendaftaran')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('image')->label('')->disk('public')->square(),
                TextColumn::make('name')->label('Program')->searchable()->description(fn (Program $r) => $r->level),
                TextColumn::make('duration')->label('Lama'),
                IconColumn::make('open_registration')->label('Pendaftaran')->boolean(),
            ])
            ->recordActions([
                EditAction::make()->slideOver(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManagePrograms::route('/')];
    }
}
