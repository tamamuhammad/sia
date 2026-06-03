<?php
namespace App\Filament\Widgets;

use App\Models\Period;
use Filament\Widgets\Widget;
use App\Models\Student;
use App\Models\Presence;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ScanPresenceWidget extends Widget
{
    // Mengatur agar widget mengambil sisa ruang penuh di dashboard
    protected int | string | array $columnSpan = 'full';

    // Menunjuk file blade custom widget
    protected string $view = 'filament.widgets.scan-presence-widget';

    /**
     * Fungsi pemroses scan dari JavaScript
     */
    public function processScan(string $encryptedPayload): void
    {
        try {
            $studentId = Crypt::decryptString($encryptedPayload);
            $student = Student::find($studentId);
            
            if (!$student) {
                $this->dispatch('scan-error', message: 'Data santri tidak ditemukan!');
                return;
            }

            $today = Carbon::today()->toDateString();
            $alreadyPresent = Presence::where('student_id', $student->id)
                ->where('presence_date', $today)
                ->exists();

            if ($alreadyPresent) {
                $this->dispatch('scan-warning', message: "{$student->name} sudah absen hari ini.");
                return;
            }

            Presence::create([
                'student_id' => $student->id,
                'presence_date' => $today,
                'status' => 'Hadir',
                'group_id' => $student->group_id,
                'period_id' => Period::where('name', session('period'))->first()?->id,
            ]);

            $this->dispatch('scan-success', message: "Beep! Presences berhasil: {$student->name}");

            Notification::make()
                ->title('Absensi Berhasil')
                ->success()
                ->send();

        } catch (DecryptException $e) {
            $this->dispatch('scan-error', message: 'QR Code Tidak Valid!');
        }
    }

    public static function canView(): bool
    {
        return in_array(Auth::user()->role_id, [1,2,3]);
    }
}
