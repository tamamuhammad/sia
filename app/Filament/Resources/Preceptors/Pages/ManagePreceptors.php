<?php

namespace App\Filament\Resources\Preceptors\Pages;

use App\Filament\Resources\Preceptors\PreceptorResource;
use App\Imports\PreceptorImport;
use App\Models\Preceptor;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class ManagePreceptors extends ManageRecords
{
    protected static string $resource = PreceptorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Ustadz')
                ->modalWidth('lg')
                ->modalHeading('Tambah Ustadz')
                ->using(function (array $data): Preceptor {
                    return DB::transaction(function () use ($data) {
                        $user = User::create([
                            'name' => $data['name'], 
                            'email' => $data['email'],
                            'password' => Hash::make("123456"),
                            'role_id' => 3,
                        ]);

                        $data['user_id'] = $user->id;

                        unset($data['email']);

                        return Preceptor::create($data);
                    });
                }),
            Action::make('importPreceptors')
                ->label('Import Data')
                ->icon('heroicon-m-arrow-up-tray')
                ->color('info')
                ->schema([
                    FileUpload::make('file')
                        ->label('Pilih File Import')
                        ->required()
                        ->rules(['mimes:xlsx,xls,csv'])
                        ->storeFiles(false)
                        ->disk('local')
                ])
                ->extraModalFooterActions([
                    Action::make('downloadTemplate')
                        ->label('Download Template')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('gray')
                        ->url(asset('templates/template_import_ustadz.xlsx'))
                ])
                ->action(function (array $data) {
                    $file = $data['file'];

                    try {
                        Excel::import(new PreceptorImport, $file);

                        Notification::make()
                            ->title('Import Sukses dan Ustadz berhasil ditambahkan')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Gagal dikarenakan: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
