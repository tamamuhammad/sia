<?php

namespace App\Filament\Resources\Reports;

use App\Filament\Resources\Reports\Pages\ManageReports;
use App\Models\Group;
use App\Models\Period;
use App\Models\Report;
use App\Models\Student;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ReportResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $modelLabel = 'Laporan Bulanan';      
    protected static ?string $pluralModelLabel = 'Laporan Bulanan';
    protected static ?string $navigationLabel = 'Laporan Bulanan';
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::PresentationChartBar;

    protected static ?string $recordTitleAttribute = 'Report';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(5)
            ->components([
                Section::make(fn ($record) => $record->name)
                    ->icon('heroicon-m-user')
                    ->schema([
                        Grid::make(14)
                            ->schema([
                                TextEntry::make('username')
                                    ->label('NIS')
                                    ->copyable()
                                    ->getStateUsing(fn ($record) => $record?->user?->username)
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-m-identification')
                                    ->color('gray')
                                    ->columnSpan(2)
                                    ->extraAttributes(['class' => '-mt-2']),

                                TextEntry::make('group.name')
                                    ->label('Kelompok Mengaji')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-m-user-group')
                                    ->columnSpan(3)
                                    ->extraAttributes(['class' => '-mt-1']),
                                
                                TextEntry::make('phone')
                                    ->label('Telepon Santri')
                                    ->icon('heroicon-m-device-phone-mobile')
                                    ->placeholder('-')
                                    ->columnSpan(3)
                                    ->extraAttributes(['class' => '-mt-2']),

                                TextEntry::make('guardian_name')
                                    ->label('Nama Wali')
                                    ->columnSpan(3)
                                    ->extraAttributes(['class' => '-mt-2']),

                                TextEntry::make('guardian_phone')
                                    ->label('Telepon Wali')
                                    ->icon('heroicon-m-phone')
                                    ->placeholder('-')
                                    ->columnSpan(3)
                                    ->extraAttributes(['class' => '-mt-2']),
                            ])
                            ->extraAttributes(['class' => 'gap-y-0']),
                    ])
                    ->compact()
                    ->columnSpan(5),
                ViewEntry::make('attendance_chart')
                    ->label('')
                    ->columnSpan(2)
                    ->view('filament.infolists.attendance-chart'),
                Grid::make(1)
                    ->columnSpan(3)
                    ->schema([
                        ViewEntry::make('attendance_chart')
                            ->label('')
                            ->view('filament.infolists.attendance-stats'),
                        Section::make('Progres Hafalan Bulan Ini')
                            ->icon('heroicon-o-book-open')
                            ->schema([
                                Grid::make(6)
                                    ->schema([
                                        TextEntry::make('reports.month')
                                            ->label('Bulan')
                                            ->inlineLabel()
                                            ->getStateUsing(function ($livewire) {
                                                $bulanFilter = $livewire->tableFilters['bulan']['value'] ?? now()->format('n');
                                                
                                                return Carbon::create(null, $bulanFilter, 1)->translatedFormat('F');
                                            })
                                            ->columnSpan(6)
                                            ->extraAttributes(['class' => 'text-right']),

                                        TextEntry::make('reports.period.name')
                                            ->label('Periode Akademik')
                                            ->inlineLabel()
                                            ->getStateUsing(fn() => session('period'))
                                            ->columnSpan(6)
                                            ->extraAttributes(['class' => 'text-right']),

                                        TextEntry::make('reports.achievement')
                                            ->label('Pencapaian Terakhir')
                                            ->inlineLabel()
                                            ->placeholder('Belum ada setoran')
                                            ->getStateUsing(function ($record, $livewire) {
                                                $bulanFilter = $livewire->tableFilters['bulan']['value'] ?? now()->format('n');
                                                
                                                $report = $record->reports()
                                                    ->where('month', $bulanFilter)
                                                    ->whereHas('period', fn($q) => $q->where('name', session('period')))
                                                    ->first();

                                                return $report?->achievement ?? 'Belum ada setoran';
                                            })
                                            ->columnSpan(6)
                                            ->extraAttributes(['class' => 'text-right']),

                                        TextEntry::make('reports.notes')
                                            ->label('Catatan')
                                            ->placeholder('Tidak ada catatan khusus')
                                            ->getStateUsing(function ($record, $livewire) {
                                                $bulanFilter = $livewire->tableFilters['bulan']['value'] ?? now()->format('n');
                                                
                                                $report = $record->reports()
                                                    ->where('month', $bulanFilter)
                                                    ->whereHas('period', fn($q) => $q->where('name', session('period')))
                                                    ->first();

                                                return $report?->notes ?? 'Tidak ada catatan khusus';
                                            })
                                            ->columnSpanFull()
                                            ->prose() 
                                            ->extraAttributes(['class' => '-mt-2'])
                                            ->color('gray'),
                                    ])
                                    ->extraAttributes(['class' => 'gap-y-2']), 
                            ])
                            ->compact()
                            ->collapsible(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Report')
            ->columns([
                TextColumn::make('username')
                    ->label('NIS')
                    ->getStateUsing(fn ($record) => $record?->user?->username)
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('group.name')
                    ->label('Kelompok Mengaji')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('group_id')
                    ->label('Kelompok Mengaji')
                    ->options(function () {
                        $user = Auth::user();

                        if ($user->role_id == 3) {
                            return Group::where('preceptor_id', $user->preceptor->id)->pluck('name', 'id');
                        }

                        if ($user->role_id == 4) {
                            return Group::where('id', $user->student->group_id)->pluck('name', 'id');
                        }

                        return Group::pluck('name', 'id');
                    })
                    ->default(function () {
                        $user = Auth::user();

                        if ($user->role_id == 3) {
                            return Group::where('preceptor_id', $user->preceptor->id)->first()?->id;
                        }

                        if ($user->role_id == 4) {
                            return $user->student->group_id;
                        }

                        return null;
                    })
                    ->searchable()
                    ->selectablePlaceholder(fn() => Auth::user()->role_id != 4)
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return $query->where('group_id', $data['value']);
                    })
                    ->indicateUsing(function (array $data) {
                        if (empty($data['value'])) {
                            return null;
                        }

                        $groupName = Group::find($data['value'])?->name;

                        return Indicator::make('Kelompok: ' . $groupName)
                                ->removable(false);
                    }),
                SelectFilter::make('bulan')
                    ->label('Pilih Bulan')
                    ->options([
                        '1' => 'Januari',
                        '2' => 'Februari',
                        '3' => 'Maret',
                        '4' => 'April',
                        '5' => 'Mei',
                        '6' => 'Juni',
                        '7' => 'Juli',
                        '8' => 'Agustus',
                        '9' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ])
                    ->default(now()->format('n')) 
                    ->native(false)
                    ->query(function ($query) {
                        return $query;
                    })
                    ->indicateUsing(function (array $data) {
                        return Indicator::make('Bulan: ' . Carbon::create(null, $data['value'], 1)->translatedFormat('F'))
                            ->removable(false);
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat')
                    ->modalWidth('4xl'),
                Action::make('report')
                    ->label('Buat/Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->visible(fn () => in_array(Auth::user()->role_id, [1, 2]))
                    ->modalWidth('2xl')
                    ->modalHeading(fn($record) => 'Buat/Edit Laporan: '. $record->name)
                    ->mountUsing(function ($schema, $record) {
                        $periodName = Session::get('period');
                        $period = Period::where('name', $periodName)->first();
                        
                        $currentMonth = now()->format('m');

                        $existingReport = Report::where('student_id', $record->id)
                            ->where('period_id', $period?->id)
                            ->where('month', $currentMonth)
                            ->first();

                        if ($existingReport) {
                            $schema->fill([
                                'period_id' => $existingReport->period_id,
                                'month' => $existingReport->month,
                                'achievement' => $existingReport->achievement,
                                'notes' => $existingReport->notes,
                            ]);
                        } else {
                            $schema->fill([
                                'period_id' => $period?->id,
                                'month' => $currentMonth,
                            ]);
                        }
                    })
                    ->schema([
                        Grid::make(2)
                            ->schema([
                            Select::make('achievement')
                                ->label('Pencapaian')
                                ->options([
                                    'Surah Al-Fatihah' => 'Surah Al-Fatihah',
                                    'Tahiyat' => 'Tahiyat',
                                    'Surah An-Nas' => 'Surah An-Nas',
                                    'Surah Al-Falaq' => 'Surah Al-Falaq',
                                    'Surah Al-Ikhlas' => 'Surah Al-Ikhlas',
                                    'Surah Al-Lahab' => 'Surah Al-Lahab',
                                    'Surah An-Nasr' => 'Surah An-Nasr',
                                    'Surah Al-Kafirun' => 'Surah Al-Kafirun',
                                    'Surah Al-Kautsar' => 'Surah Al-Kautsar',
                                    "Surah Al-Ma'un" => "Surah Al-Ma'un",
                                    'Surah Al-Quraisy' => 'Surah Al-Quraisy',
                                    'Surah Al-Fiil' => 'Surah Al-Fiil',
                                    'Surah Al-Humazah' => 'Surah Al-Humazah',
                                    "Surah Al-'Ashr" => "Surah Al-'Ashr",
                                    'Surah At-Takatsur' => 'Surah At-Takatsur',
                                    "Surah Al-Qori'ah" => "Surah Al-Qori'ah",
                                    'Surah Al-Adiyat' => 'Surah Al-Adiyat',
                                    'Surah Al-Zalzalah' => 'Surah Al-Zalzalah',
                                    'Surah Al-Bayyinah' => 'Surah Al-Bayyinah',
                                    'Surah Al-Qadr' => 'Surah Al-Qadr',
                                    "Surah Al-'Alaq" => "Surah Al-'Alaq",
                                    'Surah At-Tin' => 'Surah At-Tin',
                                    'Surah Al-Insyiroh' => 'Surah Al-Insyiroh',
                                    'Surah Ad-Dhuha' => 'Surah Ad-Dhuha',
                                    'Surah Al-Lail' => 'Surah Al-Lail',
                                    'Surah Asy-Syams' => 'Surah Asy-Syams',
                                    'Surah Al-Balad' => 'Surah Al-Balad',
                                    'Surah Al-Fajr' => 'Surah Al-Fajr',
                                    'Surah Al-Ghosyiyah' => 'Surah Al-Ghosyiyah',
                                    "Surah Al-A'la" => "Surah Al-A'la",
                                    'Surah At-Thoriq' => 'Surah At-Thoriq',
                                    'Surah Al-Buruj' => 'Surah Al-Buruj',
                                    'Surah Al-Insyiqoq' => 'Surah Al-Insyiqoq',
                                    'Surah Al-Infithor' => 'Surah Al-Infithor',
                                    'Surah At-Takwir' => 'Surah At-Takwir',
                                    "Surah 'Abasa" => "Surah 'Abasa",
                                    "Surah An-Nazi'at" => "Surah An-Nazi'at",
                                    'Surah An-Naba' => 'Surah An-Naba',
                                    'Surah Yasin' => 'Surah Yasin',
                                    'Surah Ar-Rohman' => 'Surah Ar-Rohman',
                                    "Surah Al-Waqi'ah" => "Surah Al-Waqi'ah",
                                    'Surah Al-Mulk' => 'Surah Al-Mulk',
                                    'Surah As-Sajdah' => 'Surah As-Sajdah',
                                    'Surah Ad-Dukhon' => 'Surah Ad-Dukhon',
                                    'Surah Al-Kahfi' => 'Surah Al-Kahfi',
                                    'Juz 1' => 'Juz 1',
                                    'Juz 2' => 'Juz 2',
                                    'Juz 3' => 'Juz 3',
                                    'Juz 4' => 'Juz 4',
                                    'Juz 5' => 'Juz 5',
                                    'Juz 6' => 'Juz 6',
                                    'Juz 7' => 'Juz 7',
                                    'Juz 8' => 'Juz 8',
                                    'Juz 9' => 'Juz 9',
                                    'Juz 10' => 'Juz 10',
                                    'Juz 11' => 'Juz 11',
                                    'Juz 12' => 'Juz 12',
                                    'Juz 13' => 'Juz 13',
                                    'Juz 14' => 'Juz 14',
                                    'Juz 15' => 'Juz 15',
                                    'Juz 16' => 'Juz 16',
                                    'Juz 17' => 'Juz 17',
                                    'Juz 18' => 'Juz 18',
                                    'Juz 19' => 'Juz 19',
                                    'Juz 20' => 'Juz 20',
                                    'Juz 21' => 'Juz 21',
                                    'Juz 22' => 'Juz 22',
                                    'Juz 23' => 'Juz 23',
                                    'Juz 24' => 'Juz 24',
                                    'Juz 25' => 'Juz 25',
                                    'Juz 26' => 'Juz 26',
                                    'Juz 27' => 'Juz 27',
                                    'Juz 28' => 'Juz 28',
                                    'Juz 29' => 'Juz 29',
                                    'Juz 30' => 'Juz 30',
                                ])
                                ->searchable()
                                ->native(false)
                                ->preload()
                                ->required()
                                ->columnSpanFull(),
                            Textarea::make('notes')
                                ->label('Catatan')
                                ->required()
                                ->columnSpanFull(),
                            Select::make('month')
                                ->label('Bulan')
                                ->options([
                                    '1' => 'Januari',
                                    '2' => 'Februari',
                                    '3' => 'Maret',
                                    '4' => 'April',
                                    '5' => 'Mei',
                                    '6' => 'Juni',
                                    '7' => 'Juli',
                                    '8' => 'Agustus',
                                    '9' => 'September',
                                    '10' => 'Oktober',
                                    '11' => 'November',
                                    '12' => 'Desember',
                                ])
                                ->default(fn () => now()->translatedFormat('F'))
                                ->required(),
                            Select::make('period_id')
                                ->label('Periode Akademik')
                                ->options(Period::all()->pluck('name', 'id'))
                                ->default(fn () => Period::where('name', Session::get('period'))->first()?->id)
                                ->disabled()
                                ->dehydrated()
                                ->required(),
                        ])
                    ])
                    ->action(function (array $data, $record): void {
                        Report::updateOrCreate(
                            [
                                'student_id' => $record->id,
                                'period_id' => $data['period_id'],
                                'month' => $data['month'],
                            ],
                            [
                                'achievement' => $data['achievement'],
                                'notes' => $data['notes'],
                            ]
                        );

                        Notification::make()
                            ->title('Laporan berhasil disimpan')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageReports::route('/'),
        ];
    }
}
