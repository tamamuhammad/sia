<?php

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use App\Models\Period;

new class extends Component
{
    public $selectedPeriod;
    public $periods = [];

    public function mount()
    {
        $this->periods = Period::all();

        if (Session::has('period')) {
            $this->selectedPeriod = Session::get('period');
            return;
        }

        $today = Carbon::now();
        $activePeriod = Period::where('start_date', '<=', $today)
                            ->where('end_date', '>=', $today)
                            ->first();

        $default = $activePeriod?->name ?? Period::latest()->first()?->name ?? '2025/2026';

        $this->selectedPeriod = $default;
        Session::put('period', $default);
    }

    public function updatedSelectedPeriod($value)
    {
        Session::put('period', $value);
        $this->js('window.location.reload()');
    }
};
?>

<x-filament::input.wrapper>
    <x-filament::input.select wire:model.live="selectedPeriod">
        @if($periods->isEmpty())
            <option disabled>No periods found</option>
        @else
            @foreach($periods as $period)
                <option value="{{ $period->name }}" class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    {{ $period->name }}
                </option>
            @endforeach
        @endif
    </x-filament::input.select>
</x-filament::input.wrapper>