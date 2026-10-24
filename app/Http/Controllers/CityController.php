<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::with('county')->get();
        return response()->json([
            'cities' => $cities,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'county_id' => 'required|exists:counties,id',
        ]);

        $city = City::create($request->all());

        return response()->json([
            'city' => $city,
        ]);
    }

    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);
        $city->update($request->all());

        return response()->json([
            'city' => $city,
        ]);
    }

    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return response()->json([
            'message' => 'City deleted',
        ]);
    }

    public function search(Request $request) {
        $cities = City::where('name', 'LIKE', '%' . $request->name . '%')->get();
        return response()->json([
            'cities' => $cities,
        ]);
    }

    public function filter(Request $request) {
        $cities = City::whereHas('county', function ($query) use ($request) {
            $query->where('name', $request->name);
        })->get();
        return response()->json([
            'cities' => $cities,
        ]);
    }
}
