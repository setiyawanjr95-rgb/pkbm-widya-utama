<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages\ManagePosts;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $modelLabel = 'berita';

    protected static ?string $pluralModelLabel = 'Berita & Artikel';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')->label('Judul')->required()->maxLength(255)->columnSpanFull(),
                Select::make('category')->label('Kategori')->options(Post::CATEGORIES)->default('Berita')->required(),
                DateTimePicker::make('published_at')->label('Tayang')->default(now())->seconds(false)
                    ->helperText('Kosongkan untuk simpan sebagai draf.'),
                FileUpload::make('cover')->label('Foto sampul')->image()->disk('public')->directory('posts')->maxSize(4096)
                    ->imageEditor()->columnSpanFull(),
                Textarea::make('excerpt')->label('Ringkasan (opsional)')->rows(2)->maxLength(500)->columnSpanFull(),
                RichEditor::make('body')->label('Isi berita')->required()->columnSpanFull()
                    ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('posts/isi'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover')->label('')->disk('public')->square(),
                TextColumn::make('title')->label('Judul')->searchable()->wrap()->limit(80),
                TextColumn::make('category')->label('Kategori')->badge(),
                TextColumn::make('published_at')->label('Tayang')->dateTime('d M Y H:i')->sortable()->placeholder('Draf'),
            ])
            ->filters([
                SelectFilter::make('category')->label('Kategori')->options(Post::CATEGORIES),
            ])
            ->recordActions([
                Action::make('view')->label('Lihat')->icon(Heroicon::OutlinedEye)->color('gray')
                    ->url(fn (Post $r) => route('site.post', $r), shouldOpenInNewTab: true)
                    ->visible(fn (Post $r) => $r->published_at?->isPast()),
                EditAction::make()->slideOver(),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManagePosts::route('/')];
    }
}
