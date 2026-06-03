<?php

namespace App\Imports;

use App\Models\Preceptor;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PreceptorImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                if (empty($row['nama']) || $row['email'] == 'ustfulan@gmail.com') {
                    continue;
                }

                $user = User::create([
                    'name' => $row['nama'],
                    'email' => $row['email'],
                    'password' => Hash::make($row['password'] ?? '123456'),
                    'role_id' => 3, 
                ]);

                Preceptor::create([
                    'user_id'       => $user->id,
                    'name'          => $row['nama'],
                    'phone'         => $this->formatPhoneNumber($row['no_telp']),
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