<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Product\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = $this->productService->getAllProductWithPagination($request);
        return response()->json([
            'success' => true,
            'header_filter' => Product::getLangKey(),
            'data' => ProductResource::collection($products),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->productService->createProduct($request->validated());
            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
                'message' => 'Tạo sản phẩm thành công',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo sản phẩm thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = $this->productService->getProductById($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        try {
            $product = $this->productService->getProductById($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại',
                ], 404);
            }

            $this->productService->updateProduct($id, $request->validated());
            $product = $this->productService->getProductById($id);

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
                'message' => 'Cập nhật sản phẩm thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật sản phẩm thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = $this->productService->getProductById($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại',
                ], 404);
            }

            $deleted = $this->productService->deleteProduct($id);
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xóa sản phẩm thất bại',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Xóa sản phẩm thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa sản phẩm thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }
}
