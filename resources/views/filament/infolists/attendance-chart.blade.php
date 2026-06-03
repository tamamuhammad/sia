@php
    $month = $this->tableFilters['bulan']['value'] ?? now()->format('n');
    $period = session('period');
@endphp

<div class="flex flex-col justify-start bg-white dark:bg-gray-900 rounded-xl shadow-sm" style="width: 100%">
    <div class="w-full max-w-md">
        @livewire(\App\Livewire\AttendanceMonthlyChart::class, [
            'record' => $getRecord(),
            'month' => $month,
            'periodName' => $period,
        ])
    </div>
</div>