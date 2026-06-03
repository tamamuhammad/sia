@props(['date', 'options']) 

@php
    $currentValue = $getState() ?? "-";
    $record = $getRecord();
    $studentId = $record->id;

    $isDisabled = Illuminate\Support\Facades\Auth::user()->role_id == 4;

    $bgClass = match($currentValue) {
        'Hadir' => 'bg-emerald-50 dark:bg-emerald-500/10',
        'Alfa' => 'bg-danger-50 dark:bg-danger-500/10',
        'Sakit', 'Izin' => 'bg-warning-50 dark:bg-warning-500/10',
        default => 'bg-transparent',
    };
@endphp

<div wire:key="absensi-{{ $studentId }}-{{ $date }}" class="flex items-center justify-center p-1">
    <x-filament::input.wrapper>
        <x-filament::input.select
            wire:change="updatePresence('{{ $studentId }}', '{{ $date }}', $event.target.value)"
            wire:loading.attr="disabled"
            wire:target="updatePresence"
            class="min-w-12 font-bold text-xs py-0! pl-2! pr-0! border-none bg-transparent focus:ring-0"
            :disabled="$isDisabled"
        >
            <option value="-">-</option>
            @foreach($options as $key => $label)
                <option value="{{ $label }}" @selected((string)$label === (string)$currentValue)>
                    {{ $key }}
                </option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>
</div>