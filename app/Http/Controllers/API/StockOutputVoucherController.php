<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockOutputVoucherRequest;
use App\Http\Requests\UpdateStockOutputVoucherRequest;
use App\Http\Resources\StockOutputVoucherResource;
use App\Services\StockOutputVoucher\StockOutputVoucherService;
use Illuminate\Http\Request;

class StockOutputVoucherController extends Controller
{
    public function __construct(protected StockOutputVoucherService $service)
    {
    }

    /**
     * Display a listing of stock output vouchers.
     */
    public function index(Request $request)
    {
        try {
            $vouchers = $this->service->getAllVouchersWithPagination($request);
            
            return response()->json([
                'success' => true,
                'data' => StockOutputVoucherResource::collection($vouchers),
                'pagination' => [
                    'total' => $vouchers->total(),
                    'per_page' => $vouchers->perPage(),
                    'current_page' => $vouchers->currentPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu phiếu xuất: ' . $e->getMessage(),
            ], 400);
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
                'data' => StockOutputVoucherResource::collection($vouchers),
                'pagination' => [
                    'total' => $vouchers->total(),
                    'per_page' => $vouchers->perPage(),
                    'current_page' => $vouchers->currentPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu phiếu xuất: ' . $e->getMessage(),
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
                'data' => StockOutputVoucherResource::collection($vouchers),
                'pagination' => [
                    'total' => $vouchers->total(),
                    'per_page' => $vouchers->perPage(),
                    'current_page' => $vouchers->currentPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu phiếu xuất: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Store a newly created stock output voucher.
     */
    public function store(StoreStockOutputVoucherRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            
            $voucher = $this->service->createVoucher($data);
            
            return response()->json([
                'success' => true,
                'data' => new StockOutputVoucherResource($voucher->load('warehouse', 'creator', 'items')),
                'message' => 'Tạo phiếu xuất kho thành công',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo phiếu xuất kho thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified stock output voucher.
     */
    public function show($id)
    {
        try {
            $voucher = $this->service->getVoucherById($id);
            
            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu xuất kho không tồn tại',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new StockOutputVoucherResource(
                    $voucher->load('warehouse', 'section', 'creator', 'approver', 'completer', 'items.product')
                ),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu phiếu xuất: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified stock output voucher.
     */
    public function update(UpdateStockOutputVoucherRequest $request, $id)
    {
        try {
            $voucher = $this->service->getVoucherById($id);
            
            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu xuất kho không tồn tại',
                ], 404);
            }

            $this->service->updateVoucher($id, $request->validated());
            $voucher = $this->service->getVoucherById($id);

            return response()->json([
                'success' => true,
                'data' => new StockOutputVoucherResource(
                    $voucher->load('warehouse', 'section', 'creator', 'items.product')
                ),
                'message' => 'Cập nhật phiếu xuất kho thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật phiếu xuất kho thất bại: ' . $e->getMessage(),
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
                'data' => new StockOutputVoucherResource(
                    $voucher->load('warehouse', 'items')
                ),
                'message' => 'Phiếu xuất kho đã gửi duyệt',
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
                'data' => new StockOutputVoucherResource(
                    $voucher->load('warehouse', 'approver', 'items')
                ),
                'message' => 'Phiếu xuất kho đã được duyệt',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Duyệt phiếu thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Complete voucher - deduct inventory
     */
    public function complete(Request $request, $id)
    {
        try {
            $voucher = $this->service->completeVoucher($id, auth()->id());
            
            return response()->json([
                'success' => true,
                'data' => new StockOutputVoucherResource(
                    $voucher->load('warehouse', 'completer', 'items')
                ),
                'message' => 'Phiếu xuất kho đã hoàn thành',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hoàn thành phiếu thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Cancel item in voucher
     */
    public function cancelItem(Request $request, $voucherId, $itemId)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|min:10|max:500',
            ]);

            $this->service->cancelItem($voucherId, $itemId, $validated['reason']);
            $voucher = $this->service->getVoucherById($voucherId);
            
            return response()->json([
                'success' => true,
                'data' => new StockOutputVoucherResource(
                    $voucher->load('items')
                ),
                'message' => 'Mục hàng đã bị hủy',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hủy mục hàng thất bại: ' . $e->getMessage(),
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
                'data' => new StockOutputVoucherResource($voucher),
                'message' => 'Phiếu xuất kho đã bị từ chối',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Từ chối phiếu thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete the specified stock output voucher.
     */
    public function destroy($id)
    {
        try {
            $voucher = $this->service->getVoucherById($id);
            
            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu xuất kho không tồn tại',
                ], 404);
            }

            $deleted = $this->service->deleteVoucher($id);
            
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xóa phiếu xuất kho thất bại',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Xóa phiếu xuất kho thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa phiếu xuất kho thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }
}
