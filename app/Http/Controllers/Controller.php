<?php

namespace App\Http\Controllers;

use App\Models\CarReturn;
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

        $rentals = Rental::with('car')
            ->where('user_id', $userId)
            ->where('is_returned', false)
            ->get();

        $carReturn = CarReturn::whereHas('rental', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['rental.car'])
            ->get();

        return Inertia::render('Dashboard', ['rentals' => $rentals, 'carReturn' => $carReturn]);
    }
}
