<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages\ManageTestimonials;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $modelLabel = 'testimoni';

    protected static ?string $pluralModelLabel = 'Testimoni';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->label('Nama')->required()->maxLength(255),
                TextInput::make('role')->label('Keterangan')->placeholder('Lulusan Paket C 2024'),
                Textarea::make('quote')->label('Testimoni')->required()->rows(4)->columnSpanFull(),
                FileUpload::make('photo')->label('Foto')->image()->avatar()->disk('public')->directory('testimonials'),
                Toggle::make('visible')->label('Tampil di website')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')->label('')->disk('public')->circular(),
                TextColumn::make('name')->label('Nama')->searchable()->description(fn (Testimonial $r) => $r->role),
                TextColumn::make('quote')->label('Testimoni')->limit(80)->wrap(),
                ToggleColumn::make('visible')->label('Tampil'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageTestimonials::route('/')];
    }
}
