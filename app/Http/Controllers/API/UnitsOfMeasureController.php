<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitsOfMeasureResource;
use App\Models\UnitsOfMeasure;
use Illuminate\Http\Request;

class UnitsOfMeasureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return UnitsOfMeasureResource::collection(UnitsOfMeasure::where('is_active', true)->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
