<?php

namespace App\Filament\Resources\Profiles\Pages;

use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditProfile extends EditRecord
{
    protected static string $resource = ProfileResource::class;
    protected static ?string $title = 'Profil User';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->label('Ganti Password')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->modalHeading('Ganti Password')
                ->modalDescription('Silakan masukkan password lama dan password baru Anda.')
                ->modalSubmitActionLabel('Simpan Password Baru')
                ->modalWidth('md')
                ->schema([
                    TextInput::make('current_password')
                        ->label('Password Lama')
                        ->password()
                        ->required()
                        ->revealable()
                        ->currentPassword()
                        ->validationMessages([
                            'current_password' => 'Password lama yang Anda masukkan tidak benar.',
                        ]),

                    TextInput::make('new_password')
                        ->label('Password Baru')
                        ->password()
                        ->required()
                        ->revealable()
                        ->minLength(6) 
                        ->different('current_password')
                        ->same('new_password_confirmation')
                        ->validationMessages([
                            'different' => 'Password baru tidak boleh sama dengan password lama.',
                            'same' => 'Password baru dan konfirmasi tidak cocok.',
                            'min' => 'Password minimal harus 6 karakter.'
                        ]),

                    TextInput::make('new_password_confirmation')
                        ->label('Konfirmasi Password Baru')
                        ->password()
                        ->revealable()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $user = Auth::user()->staff->user;

                    $user->update([
                        'password' => Hash::make($data['new_password']),
                    ]);

                    Notification::make()
                        ->title('Berhasil!')
                        ->body('Password Anda telah berhasil diubah.')
                        ->success()
                        ->send();
                }),
            Action::make('print_qr')
                ->label('Cetak QR Absen')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn () => in_array(Auth::user()->role_id, [2, 4]))
                ->url(fn () => route('profile.print-qr'), shouldOpenInNewTab: true)
        ];
    }

    public function mount($record = null): void
    {
        parent::mount(Auth::id());
    }

    protected function resolveRecord($key = null): Model
    {
        $user = Auth::user();

        if ($user instanceof Model && in_array($user->role_id, [2, 3, 4])) {
            return $user;
        }

        abort(403, 'Akses ditolak.');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->getRecord();

        if (in_array($user->role_id, [2, 4])) {
            $user->load('student.group');

            if ($user->student) {
                $data['student'] = $user->student->toArray();
                $data['groupName'] = $user->student->group?->name ?? '-';
            } else {
                $data['student'] = [];
                $data['groupName'] = null;
            }
        } 
        elseif ($user->role_id == 3) {
            $user->load('preceptor');

            if ($user->preceptor) {
                $data['preceptor'] = $user->preceptor->toArray();
            } else {
                $data['preceptor'] = [];
            }
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $userData = [
            'email' => $data['email'] ?? $record->email,
            'name' => $data['name'] ?? $record->name,
        ];

        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }
        
        $record->update($userData);

        if (in_array($record->role_id, [2, 4]) && isset($data['student'])) {
            if (isset($data['name'])) {
                $data['student']['name'] = $data['name'];
            }

            $record->student()->updateOrCreate(
                ['user_id' => $record->id],
                $data['student']
            );
        } 
        elseif ($record->role_id == 3 && isset($data['preceptor'])) {
            if (isset($data['name'])) {
                $data['preceptor']['name'] = $data['name'];
            }

            $record->preceptor()->updateOrCreate(
                ['user_id' => $record->id],
                $data['preceptor']
            );
        }

        return $record;
    }
}
