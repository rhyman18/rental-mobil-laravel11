<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\CarReturn;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarReturnController extends Controller
{
    public function create()
    {
        return Inertia::render('Return/Create');
    }

    public function fetchRentals(Request $request)
    {
        $request->validate([
            'license_plate' => 'required|string|min:3',
        ]);

        // Fetch rentals with related car data
        $rentals = Rental::with('car') // Eager load the `car` relationship
            ->whereHas('car', function ($query) use ($request) {
                $query->where('license_plate', $request->license_plate);
            })
            ->where('user_id', auth()->id())
            ->where('is_returned', false) // Ensure the car is not already returned
            ->get();

        return response()->json($rentals);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'rental_id' => 'required|exists:rentals,id',
            'return_date' => 'required|date|after_or_equal:today',
            'duration' => 'required|integer|min:1',
            'final_cost' => 'required|numeric|min:0',
        ]);

        // Find the rental record by ID
        $rental = Rental::where('id', $request->rental_id)->firstOrFail();
        $car = $rental->car; // Get the associated car

        // Save the return data
        CarReturn::create([
            'rental_id' => $rental->id,
            'return_date' => $request->return_date,
            'duration' => $request->duration,
            'final_cost' => $request->final_cost,
        ]);

        // Update rental as returned
        $rental->update(['is_returned' => true]);

        // Check if there are any other active rentals for the car
        $hasActiveRentals = Rental::where('car_id', $car->id)
            ->where('is_returned', false)
            ->exists();

        // Update car status if no other active rentals
        if (!$hasActiveRentals) {
            $car->update(['available' => true]);
        }

        // Redirect with success message
        return redirect()->route('dashboard')->with('success', 'Car returned successfully!');
    }

}
