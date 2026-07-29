<?php

namespace App\Filament\Resources\Presences\Pages;

use App\Filament\Resources\Presences\PresenceResource;
use App\Models\Group;
use App\Models\Period;
use App\Models\Presence;
use App\Models\Student;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;

class ManagePresences extends ManageRecords
{
    protected static string $resource = PresenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function table(Table $table): Table
    {
        $month = (int) ($this->tableFilters['month']['value'] ?? now()->month);
        $periodName = Session::get('period');
        $period = Period::where('name', $periodName)->first();
        
        $year = now()->year; 

        if ($period && $period->start_date && $period->end_date) {
            $start = Carbon::parse($period->start_date);
            $end = Carbon::parse($period->end_date);

            if ($start->year === $end->year) {
                $year = $start->year;
            } else {
                $year = ($month >= $start->month) ? $start->year : $end->year;
            }
        }

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
        
        $dateColumns = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateObj = Carbon::createFromDate($year, $month, $day);
            $dateString = $dateObj->toDateString();
            $dayName = $dateObj->locale('id')->isoFormat('ddd'); 

            $headerHtml = new HtmlString("
                <div class='flex flex-col items-center justify-center leading-none min-h-7.5'>
                    <span class='text-xl font-black text-gray-700 dark:text-gray-200'>
                        {$day}
                    </span>
                    <span class='text-[10px] font-bold text-gray-400 uppercase tracking-wide mt-1'>
                        {$dayName}
                    </span>
                </div>
            ");

            $dateColumns[] = ViewColumn::make("col_{$day}")
                ->label($headerHtml)
                ->alignment('center')
                ->view('components.native-select')
                ->getStateUsing(function (Student $record) use ($dateString) {
                    $presence = $record->presences->first(function ($item) use ($dateString) {
                        return Carbon::parse($item->presence_date)->toDateString() === $dateString;
                    });

                    return $presence ? $presence->status : '-';
                })
                ->viewData([
                    'date' => $dateString,
                    'options' => ['H' => 'Hadir', 'A' => 'Alfa', 'S' => 'Sakit', 'I' => 'Izin'],
                ]);
        }

        return $table
            ->query(fn () => 
                Student::query()
                    ->with(['presences' => function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('presence_date', [$startDate, $endDate]);
                    }])
                    ->withCount([
                        'presences as total_h' => fn($q) => $q->whereBetween('presence_date', [$startDate, $endDate])->where('status', 'Hadir'),
                        'presences as total_i' => fn($q) => $q->whereBetween('presence_date', [$startDate, $endDate])->where('status', 'Izin'),
                        'presences as total_s' => fn($q) => $q->whereBetween('presence_date', [$startDate, $endDate])->where('status', 'Sakit'),
                        'presences as total_a' => fn($q) => $q->whereBetween('presence_date', [$startDate, $endDate])->where('status', 'Alfa'),
                    ])
            )
            ->columns(array_merge(
                [
                    TextColumn::make('name')
                        ->label('Nama Santri')
                        ->searchable()
                        ->sortable()
                        ->description(fn (Student $record) => $record->user?->username ?? '-')
                        ->extraHeaderAttributes([
                            'class' => 'sticky left-0 z-10 bg-gray-50 bg-gray-100 dark:bg-gray-800',
                        ])
                        ->extraAttributes([
                            'class' => 'sticky-col-name', 
                        ]),
                ],
                $dateColumns,
                [
                    TextColumn::make('total_h')
                        ->label('H')
                        ->alignment('center')
                        ->extraHeaderAttributes(['class' => 'bg-emerald-50 dark:bg-emerald-900/50']),
                        
                    TextColumn::make('total_i')
                        ->label('I')
                        ->alignment('center')
                        ->extraHeaderAttributes(['class' => 'bg-blue-50 dark:bg-blue-900/50']),
                        
                    TextColumn::make('total_s')
                        ->label('S')
                        ->alignment('center')
                        ->extraHeaderAttributes(['class' => 'bg-warning-50 dark:bg-warning-900/50']),
                        
                    TextColumn::make('total_a')
                        ->label('A')
                        ->alignment('center')
                        ->extraHeaderAttributes(['class' => 'bg-danger-50 dark:bg-danger-900/50']),
                ]
            ))
            ->paginated(false)
            ->filters([
                SelectFilter::make('month')
                    ->label('Bulan')
                    ->options(collect(range(1, 12))->mapWithKeys(fn($m) => 
                        [$m => Carbon::create(2024, $m, 1)->locale('id')->monthName]
                    ))
                    ->indicateUsing(function (array $data) {
                        return [
                            Indicator::make('Bulan: ' . Carbon::create(0, $data['value'])->locale('id')->monthName)
                                ->removable(false),
                        ];
                    })
                    ->default(now()->month)
                    ->selectablePlaceholder(false)
                    ->query(fn($query) => $query),
                SelectFilter::make('group_id')
                    ->label('Kelompok')
                    ->relationship('group', 'name', modifyQueryUsing: function (Builder $query) {
                        $user = Auth::user();

                        if ($user->role_id == 3) {
                            return $query->where('preceptor_id', $user->preceptor->id);
                        }

                        if ($user->role_id == 4) {
                            return $query->where('id', $user->student->group_id);
                        }

                        return $query;
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
                    ->placeholder('Pilih Kelompok')
                    ->indicateUsing(function (array $data) {
                        $groupName = Group::find($data['value'])?->name ?? 'Belum Dipilih';
                        return [
                            Indicator::make('Kelompok: ' . $groupName)
                                ->removable(false),
                        ];
                    })
                    ->selectablePlaceholder(fn() => Auth::user()->role_id != 4)
                    ->query(fn($query, $data) => $query->where('group_id', $data['value'])),
            ])
            ->filtersApplyAction(
                fn (Action $action) => $action
                    ->extraAttributes([
                        'x-data' => '{ isRobot: false, originalText: \'\' }',
                        '@click' => '
                            if (!isRobot) {
                                isRobot = true;
                                let labelSpan = $el.querySelector(\'span\');
                                
                                if (labelSpan) {
                                    originalText = labelSpan.innerText;
                                    labelSpan.innerText = \'Memuat...\';
                                    $el.style.opacity = \'0.7\'; 
                                }

                                setTimeout(() => {
                                    $el.click(); 
                                    setTimeout(() => { 
                                        isRobot = false; 
                                        if (labelSpan) {
                                            labelSpan.innerText = originalText;
                                            $el.style.opacity = \'1\';
                                        }
                                    }, 200);
                                }, 500);
                            }
                        ',
                    ])
            );
    }

    public function updatePresence($studentId, $date, $status): void
    {
        $groupId = $this->tableFilters['group_id']['value'] ?? null;

        if ($status === '-') {
            Presence::where('student_id', $studentId)
                ->where('presence_date', $date)
                ->delete();
                
            return;
        }

        Presence::updateOrCreate(
            [
                'student_id' => $studentId,
                'presence_date' => $date,
            ],
            [
                'status' => $status,
                'period_id' => Period::where('name', session('period'))->first()?->id,
                'group_id' => $groupId,
            ]
        );

        Notification::make()
            ->title('Absensi Tersimpan')
            ->success()
            ->duration(1000)
            ->send();
    }
}
