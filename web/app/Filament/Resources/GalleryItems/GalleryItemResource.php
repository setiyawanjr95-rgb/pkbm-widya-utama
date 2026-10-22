<?php

namespace App\Filament\Resources\GalleryItems;

use App\Filament\Resources\GalleryItems\Pages\ManageGalleryItems;
use App\Models\GalleryItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class GalleryItemResource extends Resource
{
    protected static ?string $model = GalleryItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $modelLabel = 'foto';

    protected static ?string $pluralModelLabel = 'Galeri';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                FileUpload::make('image')->label('Foto')->image()->required()->disk('public')->directory('gallery')
                    ->maxSize(6144)->imageEditor()->columnSpanFull(),
                TextInput::make('title')->label('Judul')->required()->maxLength(255),
                DatePicker::make('taken_at')->label('Tanggal kegiatan')->default(today()),
                TextInput::make('caption')->label('Keterangan')->maxLength(255)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('taken_at', 'desc')
            ->contentGrid(['md' => 3, 'xl' => 4])
            ->columns([
                Stack::make([
                    ImageColumn::make('image')->disk('public')->imageHeight(160)->extraImgAttributes(['class' => 'w-full object-cover rounded-lg']),
                    TextColumn::make('title')->weight('bold')->searchable(),
                    TextColumn::make('taken_at')->date('d M Y')->color('gray'),
                ])->space(2),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageGalleryItems::route('/')];
    }
}
