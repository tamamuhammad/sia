<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Imports\StudentImport;
use App\Models\Student;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ManageStudents extends ManageRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Santri')
                ->modalHeading('Tambah Santri')
                ->modalFooterActionsAlignment(fn() => Alignment::End)
                ->using(function (array $data): Student {
                    return DB::transaction(function () use ($data) {
                        if (isset($data['email'])) {
                            $user = User::create([
                                'name' => $data['name'], 
                                'email' => $data['email'],
                                'password' => bcrypt(date('dmY', strtotime($data['birth_date']))),
                                'role_id' => 4,
                            ]);
        
                            $data['user_id'] = $user->id;
                        }

                        return Student::create($data);
                    });
                }),
            Action::make('importStudents')
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
                        ->url(asset('templates/template_import_santri.xlsx'))
                ])
                ->action(function (array $data) {
                    $file = $data['file'];

                    try {
                        Excel::import(new StudentImport, $file);

                        Notification::make()
                            ->title('Import Sukses dan Santri berhasil ditambahkan')
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
