<?php

namespace App\Livewire;

use App\Models\Period;
use App\Models\Presence;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AttendanceMonthlyChart extends ChartWidget
{
    protected ?string $heading = 'Statistik Kehadiran Bulanan';
    protected ?string $maxHeight = '240px';

    protected static bool $isLazy = false;
    
    public $record;
    public $month;
    public $periodName;

    protected function getData(): array
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

        $totalH = $presences->where('status', 'Hadir')->count();
        $totalI = $presences->where('status', 'Izin')->count();
        $totalS = $presences->where('status', 'Sakit')->count();
        $totalA = $presences->where('status', 'Alfa')->count();

        if ($totalH === 0 && $totalI === 0 && $totalS === 0 && $totalA === 0) {
            return [
                'datasets' => [
                    [
                        'data' => [1], 
                        'backgroundColor' => ['#e5e7eb'],
                    ],
                ],
                'labels' => ['Belum ada data bulan ini'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Hari',
                    'data' => [$totalH, $totalI, $totalS, $totalA],
                    'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'], 
                ],
            ],
            'labels' => ['Hadir (H)', 'Izin (I)', 'Sakit (S)', 'Alfa (A)'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
