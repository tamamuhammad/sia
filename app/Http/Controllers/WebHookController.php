<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class WebHookController extends Controller
{
    public function acceptCData(Request $request)
    {
        $rawData = $request->getContent();
        
        $waktu = date('Y-m-d H:i:s');
        $teks = "[$waktu] Payload Masuk:\n" . $rawData . "\n----------------------------------\n";
        
        File::append(storage_path('app/absen_monitor.txt'), $teks);

        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    public function monitor()
    {
        $path = storage_path('app/absen_monitor.txt');
        
        $logData = File::exists($path) ? File::get($path) : 'Belum ada data yang dikirim oleh mesin...';

        return view('monitor_absen', ['logData' => $logData]);
    }
    
    public function cleanMonitor()
    {
        File::put(storage_path('app/absen_monitor.txt'), '');
        return redirect('/monitor-absen');
    }
}
