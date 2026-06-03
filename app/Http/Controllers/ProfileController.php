<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ProfileController extends Controller
{
    public function printQr($studentId = null)
    {
        $user = Auth::user();
        
        if ($studentId) {
            $student = Student::findOrFail($studentId);
        } else {
            $student = $user->student; 
        }

        if (!$student) {
            abort(404, 'Data profil santri belum dilengkapi.');
        }

        $payload = Crypt::encryptString($student->id);

        return view('profile.print-qr', compact('student', 'payload'));
    }
}
