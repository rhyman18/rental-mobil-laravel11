<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use Inertia\Inertia;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::all();

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
