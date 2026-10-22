<?php

namespace App\Filament\Resources\Schedules;

use App\Filament\Resources\Schedules\Pages\ManageSchedules;
use App\Models\Course;
use App\Models\Schedule;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $modelLabel = 'jadwal';

    protected static ?string $pluralModelLabel = 'Jadwal';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('course_id')->label('Mata pelajaran')->required()->columnSpanFull()
                    ->relationship('course', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Course $record) => $record->label)
                    ->searchable()->preload(),
                DatePicker::make('date')->label('Tanggal')->required()->default(today()),
                TextInput::make('repeat_weeks')->label('Ulangi tiap minggu (berapa kali)')
                    ->numeric()->minValue(1)->maxValue(26)->default(1)
                    ->helperText('Isi 12 untuk bikin jadwal mingguan selama 12 minggu.')
                    ->visibleOn('create'),
                TimePicker::make('start_time')->label('Mulai')->required()->seconds(false),
                TimePicker::make('end_time')->label('Selesai')->required()->seconds(false)->after('start_time'),
                TextInput::make('tutor')->label('Tutor')->placeholder('Kosongkan = tutor mapel'),
                TextInput::make('room')->label('Ruang / tempat'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('course'))
            ->defaultSort('date')
            ->columns([
                TextColumn::make('date')->label('Tanggal')->date('l, d M Y')->sortable(),
                TextColumn::make('start_time')->label('Jam')
                    ->formatStateUsing(fn (Schedule $r) => substr($r->start_time, 0, 5).' - '.substr($r->end_time, 0, 5)),
                TextColumn::make('course.name')->label('Mapel')->searchable()
                    ->description(fn (Schedule $r) => "{$r->course->paket} · {$r->course->kelas}"),
                TextColumn::make('tutor')->label('Tutor')->placeholder('-'),
                TextColumn::make('room')->label('Ruang')->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('course')->label('Mapel')->relationship('course', 'name')->searchable()->preload(),
                Filter::make('upcoming')->label('Mulai hari ini')->default()
                    ->query(fn ($query) => $query->whereDate('date', '>=', today())),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSchedules::route('/'),
        ];
    }
}
