<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Pengaturan isi website: identitas, beranda, profil, kontak, pendaftaran. */
class ManageSettings extends Page
{
    protected string $view = 'filament.pages.manage-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Pengaturan Website';

    protected static ?string $title = 'Pengaturan Website';

    protected static ?int $navigationSort = 0;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public function mount(): void
    {
        $this->form->fill(Setting::allValues());
    }

    public function form(Schema $schema): Schema
    {
        $image = fn (string $name, string $label) => FileUpload::make($name)->label($label)
            ->image()->disk('public')->directory('site')->maxSize(4096)->imageEditor();

        return $schema
            ->statePath('data')
            ->components([
                Tabs::make()->persistTabInQueryString()->columnSpanFull()->tabs([
                    Tab::make('Identitas')->icon(Heroicon::OutlinedIdentification)->columns(2)->schema([
                        TextInput::make('site_name')->label('Nama lembaga')->required(),
                        TextInput::make('tagline')->label('Slogan'),
                        $image('logo', 'Logo (persegi, PNG transparan lebih bagus)'),
                        Textarea::make('about_short')->label('Deskripsi singkat (footer & mesin pencari)')->rows(3)->columnSpanFull(),
                    ]),
                    Tab::make('Beranda')->icon(Heroicon::OutlinedHome)->columns(2)->schema([
                        TextInput::make('hero_title')->label('Judul besar')->required()->columnSpanFull(),
                        Textarea::make('hero_subtitle')->label('Kalimat di bawah judul')->rows(2)->columnSpanFull(),
                        $image('hero_image', 'Foto utama (potret, misal foto kegiatan belajar)'),
                        TextInput::make('apk_url')->label('Link unduh aplikasi Android')->url(),
                        Repeater::make('stats')->label('Angka statistik')->columnSpanFull()->grid(2)
                            ->schema([TextInput::make('value')->label('Angka')->required(), TextInput::make('label')->label('Keterangan')->required()])
                            ->columns(2)->maxItems(4)->addActionLabel('Tambah angka'),
                        Repeater::make('features')->label('Keunggulan')->columnSpanFull()->grid(2)
                            ->schema([TextInput::make('title')->label('Judul')->required(), Textarea::make('text')->label('Penjelasan')->rows(2)->required()])
                            ->maxItems(4)->addActionLabel('Tambah keunggulan'),
                    ]),
                    Tab::make('Profil')->icon(Heroicon::OutlinedBuildingLibrary)->columns(2)->schema([
                        Textarea::make('history')->label('Sejarah singkat')->rows(6)->columnSpanFull()
                            ->helperText('Pisahkan paragraf dengan baris kosong.'),
                        Textarea::make('vision')->label('Visi')->rows(2)->columnSpanFull(),
                        Repeater::make('missions')->label('Misi')->simple(TextInput::make('text')->required())
                            ->columnSpanFull()->addActionLabel('Tambah misi'),
                        TextInput::make('head_name')->label('Nama kepala PKBM'),
                        $image('head_photo', 'Foto kepala PKBM'),
                        Textarea::make('head_message')->label('Kata sambutan')->rows(4)->columnSpanFull(),
                    ]),
                    Tab::make('Kontak')->icon(Heroicon::OutlinedPhone)->columns(2)->schema([
                        Textarea::make('address')->label('Alamat')->rows(2)->columnSpanFull(),
                        TextInput::make('phone')->label('Telepon'),
                        TextInput::make('whatsapp')->label('No. WhatsApp')->placeholder('08xxxxxxxxxx')
                            ->helperText('Dipakai untuk tombol "Tanya via WA" di semua halaman.'),
                        TextInput::make('email')->email(),
                        TextInput::make('office_hours')->label('Jam layanan'),
                        Textarea::make('maps_embed')->label('Peta Google Maps')->rows(3)->columnSpanFull()
                            ->helperText('Buka Google Maps > Bagikan > Sematkan peta > Salin HTML, lalu tempel di sini.'),
                        TextInput::make('facebook')->label('Link Facebook')->url(),
                        TextInput::make('instagram')->label('Link Instagram')->url(),
                        TextInput::make('youtube')->label('Link YouTube')->url(),
                    ]),
                    Tab::make('Pendaftaran')->icon(Heroicon::OutlinedClipboardDocumentCheck)->schema([
                        Toggle::make('registration_open')->label('Formulir pendaftaran online dibuka'),
                        Textarea::make('registration_info')->label('Syarat / berkas yang disiapkan')->rows(4),
                    ]),
                ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Dari kode <iframe> Google Maps, ambil link src-nya saja.
        if (preg_match('/src="([^"]+)"/', (string) $data['maps_embed'], $m)) {
            $data['maps_embed'] = html_entity_decode($m[1]);
        }

        Setting::put($data);
        $this->form->fill(Setting::allValues());

        Notification::make()->title('Pengaturan website disimpan')->success()->send();
    }
}
