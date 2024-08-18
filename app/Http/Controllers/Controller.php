<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $userId = auth()->id();

        // Fetch rentals with related car information
        $rentals = Rental::with('car') // Eager load the car relationship
            ->where('user_id', $userId)
            ->get()->map(function ($rental) {
                $rental->car->daily_rate = 'Rp. ' . number_format($rental->car->daily_rate, 0, ',', '.');
                $rental->estimated_cost = 'Rp. ' . number_format($rental->estimated_cost, 0, ',', '.');
                return $rental;
            });

        // Return the data to the Inertia view
        return Inertia::render('Dashboard', ['rentals' => $rentals]);
    }
}
