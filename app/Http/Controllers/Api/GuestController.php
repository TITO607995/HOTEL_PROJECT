<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        // Samakan persis dengan query di GuestController versi Web
        $guests = Guest::where(function($query) {
            $query->where('status', '!=', 'checked_out')
                  ->orWhereNull('status');
        })->orderBy('id', 'desc')->get(); // Pakai orderBy id desc sebagai pengganti latest() biar lebih aman

        return response()->json([
            'status' => 'success',
            'data' => $guests
        ], 200);
    }

    // 2. API TOGGLE INCOGNITO
    public function toggleIncognito($id)
    {
        try {
            $guest = Guest::findOrFail($id);
            $guest->is_incognito = !$guest->is_incognito;
            $guest->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Status privasi berhasil diperbarui!',
                'is_incognito' => $guest->is_incognito
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}