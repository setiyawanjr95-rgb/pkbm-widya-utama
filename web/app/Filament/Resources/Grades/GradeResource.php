<?php

namespace App\Filament\Resources\Grades;

use App\Filament\Resources\Grades\Pages\ManageGrades;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $modelLabel = 'nilai';

    protected static ?string $pluralModelLabel = 'Rekap Nilai';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('student_id')->label('Warga belajar')->required()->columnSpanFull()
                    ->relationship('student', 'nisn')
                    ->getOptionLabelFromRecordUsing(fn (Student $record) => "{$record->user->name} ({$record->nisn}) · {$record->kelas}")
                    ->searchable(['nisn'])->preload(),
                Select::make('course_id')->label('Mata pelajaran')->required()->columnSpanFull()
                    ->relationship('course', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Course $record) => $record->label)
                    ->searchable()->preload(),
                Select::make('category')->label('Kategori')->options(Grade::CATEGORIES)->required()->default('Ujian'),
                TextInput::make('score')->label('Nilai')->numeric()->minValue(0)->maxValue(100)->required(),
                TextInput::make('title')->label('Keterangan')->placeholder('Misal: UTS Semester 1')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['student.user', 'course']))
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('student.user.name')->label('Warga belajar')->searchable()
                    ->description(fn (Grade $r) => 'NISN '.$r->student->nisn),
                TextColumn::make('course.name')->label('Mapel')->searchable(),
                TextColumn::make('category')->label('Kategori')->badge(),
                TextColumn::make('title')->label('Keterangan')->limit(40)->placeholder('-'),
                TextColumn::make('score')->label('Nilai')->sortable()->badge()
                    ->color(fn (int $state) => $state >= 85 ? 'success' : ($state >= 70 ? 'info' : ($state >= 60 ? 'warning' : 'danger'))),
                TextColumn::make('updated_at')->label('Tanggal')->date('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('course')->label('Mapel')->relationship('course', 'name')->searchable()->preload(),
                SelectFilter::make('category')->label('Kategori')->options(Grade::CATEGORIES),
                SelectFilter::make('kelas')->options(Student::kelasOptions())
                    ->query(fn ($query, array $data) => $query->when($data['value'], fn ($q, $v) => $q->whereRelation('student', 'kelas', $v))),
            ])
            ->recordActions([
                EditAction::make()->hidden(fn (Grade $r) => $r->source_type !== null),
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
            'index' => ManageGrades::route('/'),
        ];
    }
}
