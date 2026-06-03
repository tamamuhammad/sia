<?php

namespace App\Filament\Resources\Periods\Pages;

use App\Filament\Resources\Periods\PeriodResource;
use App\Models\Period;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;

class ManagePeriods extends ManageRecords
{
    protected static string $resource = PeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Tahun Ajaran')
                ->modalWidth('lg')
                ->modalHeading('Tambah Tahun Ajaran')
                ->using(function (array $data): Period {
                    return DB::transaction(function () use ($data) {
                        if (!isset($data['name'])) {
                            $data['name'] = Carbon::parse($data['start_date'])->format('Y') . '/' . Carbon::parse($data['end_date'])->format('Y');
                        }

                        return Period::create($data);
                    });
                }),
        ];
    }
}
