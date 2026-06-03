<?php

namespace App\Livewire;

use App\Models\Period;
use App\Models\Presence;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class AttendanceMonthlyStats extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    public $record;
    public $month;
    public $periodName;

    protected function getStats(): array
    {
        $month = (int) $this->month;
        $year = now()->year;

        $period = Period::where('name', $this->periodName)->first();
        if ($period && $period->start_date && $period->end_date) {
            $start = Carbon::parse($period->start_date);
            $end = Carbon::parse($period->end_date);
            
            $year = ($start->year === $end->year) 
                ? $start->year 
                : (($month >= $start->month) ? $start->year : $end->year);
        }

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        $presences = Presence::where('student_id', $this->record->id)
            ->whereBetween('presence_date', [$startDate, $endDate])
            ->get();

        $hadir = $presences->where('status', 'Hadir')->count();
        $izin = $presences->where('status', 'Izin')->count();
        $sakit = $presences->where('status', 'Sakit')->count();
        $alfa = $presences->where('status', 'Alfa')->count();

        return [
            Stat::make('Hadir', new HtmlString("<span class='text-xl font-bold'>{$hadir} Hari</span>"))
                ->color('success'),
                
            Stat::make('Izin', new HtmlString("<span class='text-xl font-bold'>{$izin} Hari</span>"))
                ->color('info'),
                
            Stat::make('Sakit', new HtmlString("<span class='text-xl font-bold'>{$sakit} Hari</span>"))
                ->color('warning'),
                
            Stat::make('Alfa', new HtmlString("<span class='text-xl font-bold'>{$alfa} Hari</span>"))
                ->color('danger'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
