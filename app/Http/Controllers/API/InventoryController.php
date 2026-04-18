<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Http\Requests\AdjustInventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $inventoryService)
    {
    }

    /**
     * Display a listing of inventory records.
     */
    public function index(Request $request)
    {
        try {
            $inventory = $this->inventoryService->getAllInventoryWithPagination($request);
            $data = [
                'data' => InventoryResource::collection($inventory),
                'pagination' => [
                    'total' => $inventory->total(),
                    'per_page' => $inventory->perPage(),
                    'current_page' => $inventory->currentPage(),
                ]
            ];
            return $this->successResponse($data);
        } catch (\Exception $e) {
            return $this->errorResponse('Lỗi khi lấy dữ liệu kho hàng: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Store a newly created inventory record.
     */
    public function store(StoreInventoryRequest $request)
    {
        try {
            $inventory = $this->inventoryService->createInventory($request->validated());
            return $this->successResponse(new InventoryResource($inventory), 'Tạo tồn kho thành công', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Tạo tồn kho thất bại: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Display the specified inventory record.
     */
    public function show($id)
    {
        try {
            $inventory = $this->inventoryService->getInventoryById($id);
            if (!$inventory) {
                return $this->errorResponse('Tồn kho không tồn tại', 404);
            }
            return $this->successResponse(new InventoryResource($inventory));
        } catch (\Exception $e) {
            return $this->errorResponse('Lỗi khi lấy dữ liệu tồn kho: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Update the specified inventory record.
     */
    public function update(UpdateInventoryRequest $request, $id)
    {
        try {
            $inventory = $this->inventoryService->getInventoryById($id);
            if (!$inventory) {
                return $this->errorResponse('Tồn kho không tồn tại', 404);
            }
            $updatedInventory = $this->inventoryService->updateInventory($id, $request->validated());
            return $this->successResponse(new InventoryResource($updatedInventory), 'Cập nhật tồn kho thành công');
        } catch (\Exception $e) {
            return $this->errorResponse('Cập nhật tồn kho thất bại: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Remove the specified inventory record.
     */
    public function destroy($id)
    {
        try {
            $inventory = $this->inventoryService->getInventoryById($id);
            if (!$inventory) {
                return $this->errorResponse('Tồn kho không tồn tại', 404);
            }
            $this->inventoryService->deleteInventory($id);
            return $this->successResponse(null, 'Xóa tồn kho thành công');
        } catch (\Exception $e) {
            return $this->errorResponse('Xóa tồn kho thất bại: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Get inventory by warehouse
     */
    public function byWarehouse(Request $request, $warehouseId)
    {
        try {
            $inventory = $this->inventoryService->getInventoryByWarehouse($warehouseId, $request);
            
            return response()->json([
                'success' => true,
                'data' => InventoryResource::collection($inventory),
                'pagination' => [
                    'total' => $inventory->total(),
                    'per_page' => $inventory->perPage(),
                    'current_page' => $inventory->currentPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu kho hàng: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get inventory by product
     */
    public function byProduct(Request $request, $productId)
    {
        try {
            $inventory = $this->inventoryService->getInventoryByProduct($productId, $request);
            
            return response()->json([
                'success' => true,
                'data' => InventoryResource::collection($inventory),
                'pagination' => [
                    'total' => $inventory->total(),
                    'per_page' => $inventory->perPage(),
                    'current_page' => $inventory->currentPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu kho hàng: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get low stock items
     */
    public function lowStock(Request $request)
    {
        try {
            $inventory = $this->inventoryService->getLowStockItems($request);
            
            return response()->json([
                'success' => true,
                'data' => InventoryResource::collection($inventory),
                'pagination' => [
                    'total' => $inventory->total(),
                    'per_page' => $inventory->perPage(),
                    'current_page' => $inventory->currentPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu kho hàng: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Adjust inventory quantities
     */
    public function adjust($id, AdjustInventoryRequest $request)
    {
        try {
            $inventory = $this->inventoryService->getInventoryById($id);
            
            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bản ghi kho hàng không tồn tại',
                ], 404);
            }

            $this->inventoryService->adjustInventory($id, $request->validated());
            $inventory = $this->inventoryService->getInventoryById($id);

            return response()->json([
                'success' => true,
                'data' => new InventoryResource($inventory),
                'message' => 'Điều chỉnh kho hàng thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Điều chỉnh kho hàng thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }
}
