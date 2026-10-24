<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\County;

class CountyController extends Controller
{
    public function index()
    {
        $counties = County::with('cities')->get();
        return response()->json([
            'counties' => $counties,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $county = County::create($request->all());

        return response()->json([
            'county' => $county,
        ]);
    }

    public function update(Request $request, $id)
    {
        $county = County::findOrFail($id);
        $county->update($request->all());

        return response()->json([
            'county' => $county,
        ]);
    }

    public function destroy($id)
    {
        $county = County::findOrFail($id);
        $county->delete();

        return response()->json([
            'message' => 'County deleted',
        ]);
    }
    
    public function search(Request $request) {
        $counties = County::where('name', 'LIKE', '%' . $request->name . '%')->get();
        return response()->json([
            'counties' => $counties,
        ]);
    }
}
