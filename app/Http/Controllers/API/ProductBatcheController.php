<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdductBatchResource;
use App\Models\Product;
use App\Models\ProductBatche;
use App\Services\ProductBatch\ProductBatcheService;
use Illuminate\Http\Request;

class ProductBatcheController extends Controller
{

    public function __construct(protected ProductBatcheService $productBatchService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product, Request $request)
    {
        $productBatchs = $this->productBatchService->getBatchesByProductId($product->id, $request->input('per_page', 15));
        
        $data = [
            'header_filter' => ProductBatche::getLangKey(),
            'data' => ProdductBatchResource::collection($productBatchs),
            'pagination' => [
                'total' => $productBatchs->total(),
                'per_page' => $productBatchs->perPage(),
                'current_page' => $productBatchs->currentPage(),
            ]
        ];

        return $this->successResponse($data);
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
