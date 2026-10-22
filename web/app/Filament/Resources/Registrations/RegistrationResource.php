<?php

namespace App\Filament\Resources\Registrations;

use App\Filament\Resources\Registrations\Pages\ManageRegistrations;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Registration;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Pendaftar dari formulir online di website. */
class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $modelLabel = 'pendaftar';

    protected static ?string $pluralModelLabel = 'Pendaftaran Online';

    protected static ?int $navigationSort = -1;

    public static function getNavigationBadge(): ?string
    {
        return Registration::where('status', 'baru')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')->options(Registration::STATUSES)->required(),
                Textarea::make('admin_notes')->label('Catatan pengelola')->rows(3),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Data pendaftar')->columnSpanFull()->columns(2)->schema([
                    TextEntry::make('code')->label('Kode')->copyable()->weight('bold'),
                    TextEntry::make('program.name')->label('Program'),
                    TextEntry::make('name')->label('Nama lengkap'),
                    TextEntry::make('nik')->label('NIK')->placeholder('-'),
                    TextEntry::make('birth_place')->label('Tempat, tanggal lahir')
                        ->formatStateUsing(fn (Registration $r) => $r->birth_place.', '.$r->birth_date?->translatedFormat('d F Y')),
                    TextEntry::make('gender')->label('Jenis kelamin')->formatStateUsing(fn ($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                    TextEntry::make('last_education')->label('Pendidikan terakhir')->placeholder('-'),
                    TextEntry::make('phone')->label('No. HP / WA')->url(fn (Registration $r) => $r->whatsappUrl(), shouldOpenInNewTab: true)->color('success'),
                    TextEntry::make('email')->placeholder('-'),
                    TextEntry::make('created_at')->label('Mendaftar')->dateTime('d M Y H:i'),
                    TextEntry::make('address')->label('Alamat')->columnSpanFull(),
                    TextEntry::make('notes')->label('Catatan pendaftar')->placeholder('-')->columnSpanFull(),
                ]),
                Section::make('Tindak lanjut')->columnSpanFull()->columns(2)->schema([
                    TextEntry::make('status')->badge()->formatStateUsing(fn ($state) => Registration::STATUSES[$state] ?? $state),
                    TextEntry::make('admin_notes')->label('Catatan pengelola')->placeholder('-'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('program'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->description(fn (Registration $r) => $r->code),
                TextColumn::make('program.name')->label('Program'),
                TextColumn::make('phone')->label('No. HP / WA')->searchable(),
                TextColumn::make('created_at')->label('Mendaftar')->since()->sortable(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => Registration::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) { 'baru' => 'warning', 'dihubungi' => 'info', 'diterima' => 'success', default => 'danger' }),
            ])
            ->filters([
                SelectFilter::make('status')->options(Registration::STATUSES),
                SelectFilter::make('program')->relationship('program', 'name'),
            ])
            ->recordActions([
                Action::make('whatsapp')->label('WA')->icon(Heroicon::OutlinedChatBubbleLeftRight)->color('success')
                    ->url(fn (Registration $r) => $r->whatsappUrl(), shouldOpenInNewTab: true),
                ViewAction::make(),
                EditAction::make()->label('Tindak lanjut'),
                Action::make('makeStudent')->label('Buat akun siswa')->icon(Heroicon::OutlinedUserPlus)
                    ->visible(fn (Registration $r) => $r->status === 'diterima')
                    ->url(fn (Registration $r) => StudentResource::getUrl('create', ['pendaftar' => $r->id])),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageRegistrations::route('/')];
    }
}
