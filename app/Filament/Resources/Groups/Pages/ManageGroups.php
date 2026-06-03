<?php

namespace App\Filament\Resources\Groups\Pages;

use App\Filament\Resources\Groups\GroupResource;
use App\Models\Group;
use App\Models\Preceptor;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;

class ManageGroups extends ManageRecords
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('lg')
                ->using(function (array $data): Group {
                    return DB::transaction(function () use ($data) {
                        if (!isset($data['name'])) {
                            $preceptorName = Preceptor::find($data['preceptor_id'])?->name ?? 'N/A';
                            $data['name'] = $preceptorName;
                        }

                        return Group::create($data);
                    });
                }),
        ];
    }
}
