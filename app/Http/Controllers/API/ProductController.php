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
        $data = [
            'header_filter' => Product::getLangKey(),
            'data' => ProductResource::collection($products),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
            ],
        ];
        return $this->successResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->productService->createProduct($request->validated());
            return $this->createdResponse(new ProductResource($product), 'Tạo sản phẩm thành công');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Tạo sản phẩm thất bại: ' . $e->getMessage(), $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = $this->productService->getProductById($id);
        
        if (!$product) {
            return $this->notFoundResponse('Sản phẩm không tồn tại');
        }

        return $this->successResponse(new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        try {
            $product = $this->productService->getProductById($id);
            
            if (!$product) {
                return $this->notFoundResponse('Sản phẩm không tồn tại');
            }

            $this->productService->updateProduct($id, $request->validated());
            $product = $this->productService->getProductById($id);

            return $this->successResponse(new ProductResource($product), 'Cập nhật sản phẩm thành công');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Cập nhật sản phẩm thất bại: ' . $e->getMessage(), $e);
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
                return $this->notFoundResponse('Sản phẩm không tồn tại');
            }

            $this->productService->deleteProduct($id);

            return $this->noContentResponse('Xóa sản phẩm thành công');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Xóa sản phẩm thất bại: ' . $e->getMessage(), $e);
        }
    }

    // Check duplicate product code, sku
    public function checkDuplicate(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $id = $request->input('id'); // Optional ID for edit mode

        if (!$this->productService->checkDuplicate($field, $value, $id)) {
            return $this->successResponse(['is_duplicate' => false]);
        }

        return $this->successResponse(['is_duplicate' => true],  'Giá trị ' . $value . ' đã tồn tại');
    }
}
