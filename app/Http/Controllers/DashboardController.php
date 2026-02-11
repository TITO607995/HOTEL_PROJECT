<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data simulasi (nanti bisa diambil dari Database)
        $username = "Resepsionis";
        
        $rooms = [
            ['no' => '101', 'type' => 'Deluxe', 'status' => 'Occupied', 'color' => 'bg-red-500'],
            ['no' => '102', 'type' => 'Suite', 'status' => 'Dirty', 'color' => 'bg-yellow-400'],
            ['no' => '103', 'type' => 'Standard', 'status' => 'Available', 'color' => 'bg-green-500'],
            ['no' => '104', 'type' => 'Standard', 'status' => 'In-house', 'color' => 'bg-orange-400'],
            ['no' => '105', 'type' => 'Executive', 'status' => 'Available', 'color' => 'bg-green-500']
        ];

        $stats = [
            'Standard' => ['total' => 15, 'sisa' => 10],
            'Suite' => ['total' => 10, 'sisa' => 5],
            'Deluxe' => ['total' => 5, 'sisa' => 1]
        ];

        return view('dashboard', compact('username', 'rooms', 'stats'));
    }
}