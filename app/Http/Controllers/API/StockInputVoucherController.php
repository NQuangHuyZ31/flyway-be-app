<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockInputVoucherRequest;
use App\Http\Requests\UpdateStockInputVoucherRequest;
use App\Http\Resources\StockInputVoucherResource;
use App\Services\StockInputVoucher\StockInputVoucherService;
use Illuminate\Http\Request;

class StockInputVoucherController extends Controller
{
    public function __construct(protected StockInputVoucherService $service)
    {
    }

    /**
     * Display a listing of stock input vouchers.
     */
    public function index(Request $request)
    {
        try {
            $vouchers = $this->service->getAllVouchersWithPagination($request);
            $data = [
                'data' => StockInputVoucherResource::collection($vouchers),
                'pagination' => [
                    'total' => $vouchers->total(),
                    'per_page' => $vouchers->perPage(),
                    'current_page' => $vouchers->currentPage(),
                ]
            ];
            return $this->successResponse($data);
        } catch (\Exception $e) {
            return $this->errorResponse('Lỗi khi lấy dữ liệu phiếu nhập: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Get vouchers by warehouse
     */
    public function byWarehouse(Request $request, $warehouseId)
    {
        try {
            $vouchers = $this->service->getVouchersByWarehouse($warehouseId, $request);
            $data = [
                'data' => StockInputVoucherResource::collection($vouchers),
                'pagination' => [
                    'total' => $vouchers->total(),
                    'per_page' => $vouchers->perPage(),
                    'current_page' => $vouchers->currentPage(),
                ]
            ];
            return $this->successResponse($data);
        } catch (\Exception $e) {
            return $this->errorResponse('Lỗi khi lấy dữ liệu phiếu nhập: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Get vouchers by status
     */
    public function byStatus(Request $request, $statusId)
    {
        try {
            $vouchers = $this->service->getVouchersByStatus($statusId, $request);
            $data = [
                'data' => StockInputVoucherResource::collection($vouchers),
                'pagination' => [
                    'total' => $vouchers->total(),
                    'per_page' => $vouchers->perPage(),
                    'current_page' => $vouchers->currentPage(),
                ]
            ];
            return $this->successResponse($data);
        } catch (\Exception $e) {
            return $this->errorResponse('Lỗi khi lấy dữ liệu phiếu nhập: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Store a newly created stock input voucher.
     */
    public function store(StoreStockInputVoucherRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            $voucher = $this->service->createVoucher($data);
            return $this->successResponse(new StockInputVoucherResource($voucher), 'Tạo phiếu nhập thành công', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Tạo phiếu nhập thất bại: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Display the specified stock input voucher.
     */
    public function show($id)
    {
        try {
            $voucher = $this->service->getVoucherById($id);
            if (!$voucher) {
                return $this->errorResponse('Phiếu nhập không tồn tại', 404);
            }
            return $this->successResponse(new StockInputVoucherResource($voucher));
        } catch (\Exception $e) {
            return $this->errorResponse('Lỗi khi lấy dữ liệu phiếu nhập: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Update the specified stock input voucher.
     */
    public function update(UpdateStockInputVoucherRequest $request, $id)
    {
        try {
            $voucher = $this->service->getVoucherById($id);
            if (!$voucher) {
                return $this->errorResponse('Phiếu nhập không tồn tại', 404);
            }
            $updatedVoucher = $this->service->updateVoucher($id, $request->validated());
            return $this->successResponse(new StockInputVoucherResource($updatedVoucher), 'Cập nhật phiếu nhập thành công');
        } catch (\Exception $e) {
            return $this->errorResponse('Cập nhật phiếu nhập thất bại: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Submit a stock input voucher for approval.
     */
    public function submit(Request $request, $id)
    {
        try {
            $voucher = $this->service->submitVoucher($id);
            return $this->successResponse(new StockInputVoucherResource($voucher), 'Gửi phiếu nhập thành công');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Approve a stock input voucher.
     */
    public function approve(Request $request, $id)
    {
        try {
            $data = ['approved_by' => auth()->id()];
            $voucher = $this->service->approveVoucher($id, $data);
            return $this->successResponse(new StockInputVoucherResource($voucher), 'Duyệt phiếu nhập thành công');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Receive items for a stock input voucher.
     */
    public function receive(Request $request, $id)
    {
        try {
            $data = $request->all();
            $data['received_by'] = auth()->id();
            $voucher = $this->service->receiveVoucher($id, $data);
            return $this->successResponse(new StockInputVoucherResource($voucher), 'Nhận hàng thành công');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Reject a stock input voucher.
     */
    public function reject(Request $request, $id)
    {
        try {
            $reason = $request->input('reason');
            $voucher = $this->service->rejectVoucher($id, $reason);
            return $this->successResponse(new StockInputVoucherResource($voucher), 'Từ chối phiếu nhập thành công');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Remove the specified stock input voucher.
     */
    public function destroy($id)
    {
        try {
            $voucher = $this->service->getVoucherById($id);
            if (!$voucher) {
                return $this->errorResponse('Phiếu nhập không tồn tại', 404);
            }
            $this->service->deleteVoucher($id);
            return $this->successResponse(null, 'Xóa phiếu nhập thành công');
        } catch (\Exception $e) {
            return $this->errorResponse('Xóa phiếu nhập thất bại: ' . $e->getMessage(), 400);
        }
    }
}

    /**
     * Get vouchers by warehouse
     */
    public function byWarehouse(Request $request, $warehouseId)
    {
        try {
            $vouchers = $this->service->getVouchersByWarehouse($warehouseId, $request);
            
            return response()->json([
                'success' => true,
                'data' => StockInputVoucherResource::collection($vouchers),
                'pagination' => [
                    'total' => $vouchers->total(),
                    'per_page' => $vouchers->perPage(),
                    'current_page' => $vouchers->currentPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu phiếu nhập: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get vouchers by status
     */
    public function byStatus(Request $request, $statusId)
    {
        try {
            $vouchers = $this->service->getVouchersByStatus($statusId, $request);
            
            return response()->json([
                'success' => true,
                'data' => StockInputVoucherResource::collection($vouchers),
                'pagination' => [
                    'total' => $vouchers->total(),
                    'per_page' => $vouchers->perPage(),
                    'current_page' => $vouchers->currentPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu phiếu nhập: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Store a newly created stock input voucher.
     */
    public function store(StoreStockInputVoucherRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            
            $voucher = $this->service->createVoucher($data);
            
            return response()->json([
                'success' => true,
                'data' => new StockInputVoucherResource($voucher->load('supplier', 'warehouse', 'creator', 'items')),
                'message' => 'Tạo phiếu nhập kho thành công',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo phiếu nhập kho thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified stock input voucher.
     */
    public function show($id)
    {
        try {
            $voucher = $this->service->getVoucherById($id);
            
            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu nhập kho không tồn tại',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new StockInputVoucherResource(
                    $voucher->load('supplier', 'warehouse', 'section', 'creator', 'approver', 'receiver', 'items.product')
                ),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu phiếu nhập: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified stock input voucher.
     */
    public function update(UpdateStockInputVoucherRequest $request, $id)
    {
        try {
            $voucher = $this->service->getVoucherById($id);
            
            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu nhập kho không tồn tại',
                ], 404);
            }

            $this->service->updateVoucher($id, $request->validated());
            $voucher = $this->service->getVoucherById($id);

            return response()->json([
                'success' => true,
                'data' => new StockInputVoucherResource(
                    $voucher->load('supplier', 'warehouse', 'section', 'creator', 'items.product')
                ),
                'message' => 'Cập nhật phiếu nhập kho thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật phiếu nhập kho thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Submit voucher for approval
     */
    public function submit(Request $request, $id)
    {
        try {
            $voucher = $this->service->submitVoucher($id);
            
            return response()->json([
                'success' => true,
                'data' => new StockInputVoucherResource(
                    $voucher->load('supplier', 'warehouse', 'items')
                ),
                'message' => 'Phiếu nhập kho đã gửi duyệt',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gửi phiếu thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Approve voucher
     */
    public function approve(Request $request, $id)
    {
        try {
            $voucher = $this->service->approveVoucher($id, auth()->id());
            
            return response()->json([
                'success' => true,
                'data' => new StockInputVoucherResource(
                    $voucher->load('supplier', 'warehouse', 'approver', 'items')
                ),
                'message' => 'Phiếu nhập kho đã được duyệt',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Duyệt phiếu thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Receive voucher items
     */
    public function receive(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'received_items' => 'required|array',
                'received_items.*.item_id' => 'required|exists:stock_in_items,id',
                'received_items.*.quantity_received' => 'required|numeric|min:0',
                'received_items.*.quantity_rejected' => 'nullable|numeric|min:0',
                'received_items.*.rejection_notes' => 'nullable|string',
            ]);

            $voucher = $this->service->receiveVoucher($id, auth()->id(), $validated['received_items']);
            
            return response()->json([
                'success' => true,
                'data' => new StockInputVoucherResource(
                    $voucher->load('supplier', 'warehouse', 'receiver', 'items')
                ),
                'message' => 'Phiếu nhập kho đã được nhận',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Nhận phiếu thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Reject voucher
     */
    public function reject(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|min:10|max:500',
            ]);

            $voucher = $this->service->rejectVoucher($id, $validated['reason']);
            
            return response()->json([
                'success' => true,
                'data' => new StockInputVoucherResource($voucher),
                'message' => 'Phiếu nhập kho đã bị từ chối',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Từ chối phiếu thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete the specified stock input voucher.
     */
    public function destroy($id)
    {
        try {
            $voucher = $this->service->getVoucherById($id);
            
            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu nhập kho không tồn tại',
                ], 404);
            }

            $deleted = $this->service->deleteVoucher($id);
            
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xóa phiếu nhập kho thất bại',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Xóa phiếu nhập kho thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa phiếu nhập kho thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }
}
