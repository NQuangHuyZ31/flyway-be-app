<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Services\Warehouse\WarehouseService;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function __construct(protected WarehouseService $warehouseService)
    {
    }

    /**
     * Display a listing of warehouses.
     */
    public function index(Request $request)
    {
        try {
            $warehouses = $this->warehouseService->getAllWarehousesWithPagination($request);
            
            return response()->json([
                'success' => true,
                'data' => WarehouseResource::collection($warehouses),
                'pagination' => [
                    'total' => $warehouses->total(),
                    'per_page' => $warehouses->perPage(),
                    'current_page' => $warehouses->currentPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu kho: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get active warehouses only
     */
    public function active(Request $request)
    {
        try {
            $warehouses = $this->warehouseService->getAllActiveWarehouses($request);
            
            return response()->json([
                'success' => true,
                'data' => WarehouseResource::collection($warehouses),
                'pagination' => [
                    'total' => $warehouses->total(),
                    'per_page' => $warehouses->perPage(),
                    'current_page' => $warehouses->currentPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu kho: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Store a newly created warehouse.
     */
    public function store(StoreWarehouseRequest $request)
    {
        try {
            $warehouse = $this->warehouseService->createWarehouse($request->validated());
            
            return response()->json([
                'success' => true,
                'data' => new WarehouseResource($warehouse),
                'message' => 'Tạo kho thành công',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo kho thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified warehouse.
     */
    public function show($id)
    {
        try {
            $warehouse = $this->warehouseService->getWarehouseById($id);
            
            if (!$warehouse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kho không tồn tại',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new WarehouseResource($warehouse),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu kho: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified warehouse.
     */
    public function update(UpdateWarehouseRequest $request, $id)
    {
        try {
            $warehouse = $this->warehouseService->getWarehouseById($id);
            
            if (!$warehouse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kho không tồn tại',
                ], 404);
            }

            $this->warehouseService->updateWarehouse($id, $request->validated());
            $warehouse = $this->warehouseService->getWarehouseById($id);

            return response()->json([
                'success' => true,
                'data' => new WarehouseResource($warehouse),
                'message' => 'Cập nhật kho thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật kho thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete the specified warehouse.
     */
    public function destroy($id)
    {
        try {
            $warehouse = $this->warehouseService->getWarehouseById($id);
            
            if (!$warehouse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kho không tồn tại',
                ], 404);
            }

            $deleted = $this->warehouseService->deleteWarehouse($id);
            
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xóa kho thất bại',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Xóa kho thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa kho thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }
}
