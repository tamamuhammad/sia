@php
    $month = $this->tableFilters['bulan']['value'] ?? now()->format('n');
    $period = session('period');
@endphp

<style>
    .presence-stats .fi-wi-stats-overview-stat {
        padding: 0.75rem 1rem !important;
    }

    .presence-stats .fi-wi-stats-overview-stat .fi-wi-stats-overview-stat-content {
        gap: 0 !important;
    }

    .presence-stats .fi-wi-stats-overview-stat .fi-wi-stats-overview-stat-content .fi-wi-stats-overview-stat-value {
        line-height: 0 !important;
    }
</style>

<div class="flex flex-col justify-start bg-white dark:bg-gray-900 rounded-xl shadow-sm" style="width: 100%">
    <div class="w-full presence-stats">
        @livewire(\App\Livewire\AttendanceMonthlyStats::class, [
            'record' => $getRecord(),
            'month' => $month,
            'periodName' => $period,
        ])
    </div>
</div>