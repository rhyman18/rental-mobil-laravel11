<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class RentalController extends Controller
{
    public function create()
    {
        return Inertia::render('Rent/Create');
    }

    public function fetchAvailableCars(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Fetch cars that are available (not rented out during the selected dates)
        $availableCars = Car::whereDoesntHave('rental', function ($query) use ($startDate, $endDate) {
            $query->where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate)
                    ->where('is_returned', false);
            });
        })->get();

        return response()->json(['availableCars' => $availableCars]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->back()->withErrors('User not authenticated.');
        }

        $user_id = auth()->id();

        // Check if car exists and is available
        $car = Car::find($request->car_id);
        if (!$car) {
            return redirect()->back()->withErrors('Car not found.');
        }

        if (!$car->available) {
            return redirect()->back()->withErrors('Car is not available.');
        }

        // Calculate duration in days
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $duration = $endDate->diffInDays($startDate);

        // Calculate estimated price
        $estimatedPrice = $car->daily_rate * $duration;

        // Save rental
        Rental::create([
            'user_id' => $user_id,
            'car_id' => $request->car_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'duration' => $duration,
            'estimated_cost' => $estimatedPrice,
        ]);

        // Mark car as unavailable
        $car->update(['available' => false]);

        return redirect()->route('dashboard')->with('success', 'Car rented successfully!');
    }
}
