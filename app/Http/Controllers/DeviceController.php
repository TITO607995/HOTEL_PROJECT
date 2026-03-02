<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeviceController extends Controller
{
    public function index()
    {
        // 1. Ambil session dan join user
        $sessions = DB::table('sessions')
            ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
            ->select('sessions.*', 'users.name as user_name', 'users.email')
            ->orderBy('last_activity', 'desc')
            ->get();

        // 2. Mapping data (Pastikan KEY sesuai dengan yang dipanggil di BLADE)
        $devices = $sessions->map(function ($session) {
            $agent = $session->user_agent;

            return [
                'id'            => $session->id,
                'user_name'     => $session->user_name ?: 'Guest / Belum Login',
                'email'         => $session->email ?: 'Tidak Ada Email',
                'ip_address'    => $session->ip_address,
                'last_active'   => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                'is_current'    => $session->id === session()->getId(),
                
                // Key ini yang dicari oleh Blade kamu:
                'device_type'   => $this->getDeviceType($agent),
                'platform'      => $this->getPlatform($agent), // Ini dia yang bikin error tadi!
                'browser'       => $this->getBrowser($agent),
            ];
        });

        return view('admin.devices', compact('devices'));
    }

    public function logoutDevice($id)
    {
        DB::table('sessions')->where('id', $id)->delete();
        return back()->with('success', 'Perangkat berhasil diputus!');
    }

    // Helper untuk deteksi platform (Windows, Android, dll)
    private function getPlatform($agent) {
        if (preg_match('/windows/i', $agent)) return 'Windows';
        if (preg_match('/android/i', $agent)) return 'Android';
        if (preg_match('/iphone|ipad/i', $agent)) return 'iOS (iPhone/iPad)';
        if (preg_match('/macintosh|mac os x/i', $agent)) return 'Mac OS';
        if (preg_match('/linux/i', $agent)) return 'Linux';
        return 'OS Tidak Dikenal';
    }

    // Helper untuk deteksi browser
    private function getBrowser($agent) {
        if (preg_match('/chrome/i', $agent)) return 'Google Chrome';
        if (preg_match('/firefox/i', $agent)) return 'Mozilla Firefox';
        if (preg_match('/safari/i', $agent) && !preg_match('/chrome/i', $agent)) return 'Safari';
        if (preg_match('/edge/i', $agent)) return 'Microsoft Edge';
        return 'Browser Lainnya';
    }

    // Helper untuk icon perangkat
    private function getDeviceType($agent) {
        if (preg_match('/mobile|android|iphone|ipad/i', $agent)) return '📱 Mobile';
        return '💻 Desktop';
    }
}