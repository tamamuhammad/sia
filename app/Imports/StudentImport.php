<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StudentImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                if (empty($row['nis']) || empty($row['nama'])) {
                    continue;
                }

                if ($row['nis'] == '20002') {
                    continue;
                }

                $tanggalLahirRaw = $row['tanggal_lahir'];

                if (is_numeric($tanggalLahirRaw)) {
                    $date = Date::excelToDateTimeObject($tanggalLahirRaw);
                    $carbonDate = Carbon::instance($date);
                } else {
                    $carbonDate = Carbon::parse($tanggalLahirRaw);
                }

                $passwordString = $carbonDate->format('dmY');

                $user = User::create([
                    'name' => $row['nama'],
                    'username' => $row['nis'],
                    'password' => Hash::make($passwordString),
                    'role_id' => 4, 
                ]);

                Student::create([
                    'user_id'       => $user->id,
                    'name'          => $row['nama'],
                    'birth_place'   => $row['tempat_lahir'],
                    'birth_date'    => $carbonDate->toDateString(),
                    'phone'         => $this->formatPhoneNumber($row['no_telp']),
                    'guardian_name' => $row['nama_wali'],
                    'guardian_phone'=> $this->formatPhoneNumber($row['no_telp_wali']),
                ]);
            }
        });
    }

    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $phone);

        if (strlen($cleanPhone) >= 10) {
            return preg_replace('/^(\d{4})(\d{4})(\d+)$/', '$1-$2-$3', $cleanPhone);
        }

        return $cleanPhone;
    }
}