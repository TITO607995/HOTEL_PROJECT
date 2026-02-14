<?php

namespace App\Http\Controllers;

use App\Models\Guest; // <--- PASTIKAN ADA INI
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        // Mengambil data dari tabel guests
        $guests = Guest::all(); 

        // Mengirim variabel $guests ke file blade
        // Pastikan nama file blade kamu adalah 'guests.index' (atau sesuaikan)
        return view('guests.index', compact('guests')); 
    }

    public function toggleIncognito($id)
    {
        $guest = Guest::findOrFail($id);
        $guest->is_incognito = !$guest->is_incognito;
        $guest->save();

        return back()->with('success', 'Status updated!');
    }
}