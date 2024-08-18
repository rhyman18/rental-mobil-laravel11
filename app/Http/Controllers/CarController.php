<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use Inertia\Inertia;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::all()->map(function ($car) {
            $car->available = $car->available == 1 ? 'Tersedia' : 'Disewa';
            $car->daily_rate = 'Rp. ' . number_format($car->daily_rate, 0, ',', '.');
            return $car;
        });

        return Inertia::render('Cars/Index', ['cars' => $cars]);
    }

    public function create()
    {
        return Inertia::render('Cars/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand' => 'required|string',
            'model' => 'required|string',
            'license_plate' => 'required|string|unique:cars',
            'daily_rate' => 'required|numeric',
        ]);

        Car::create($request->all());

        return redirect()->route('cars.index')->with('success', 'Car has been added successfully!');
    }
}
