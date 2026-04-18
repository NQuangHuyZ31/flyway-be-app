<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnitStoreRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Traits\FilterDataTrait;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    use FilterDataTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $mode = $request->input('mode');

        if (!isset($mode) || empty($mode) || (isset($mode) && $mode !== 'list')) {
            return $this->successResponse(UnitResource::collection(Unit::all()->where('is_active', '1')));
        }

        $perPage = $request->input('per_page', 10);
        $filters = $request->input('filters', []);

        $query = Unit::query();
        $query = $this->applyFilters($query, $filters, Unit::FILTERS);
        $units = $query->paginate($perPage);

        $data = [
            'header_filter' => Unit::getLangKey(),
            'data' => UnitResource::collection($units),
            'pagination' => [
                'total' => $units->total(),
                'per_page' => $units->perPage(),
                'current_page' => $units->currentPage(),
            ],
        ];

        return $this->successResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UnitStoreRequest $request)
    {
        //
        $data = $request->all();

        $unit = Unit::create($data);

        return $this->successResponse(new UnitResource($unit), 'Tạo đơn vị thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->successResponse(new UnitResource(Unit::findOrFail($id)));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UnitStoreRequest $request, string $id)
    {
        try {
            $data = $request->all();

            $unit = Unit::findOrFail($id);
            $unit->update($data);

            return $this->successResponse(new UnitResource($unit), 'Cập nhật đơn vị thành công');
        } catch (\Exception $e) {
            return $this->errorResponse('Lỗi khi cập nhật đơn vị', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $unit = Unit::findOrFail($id);
            $unit->delete();

            return $this->noContentResponse();
        } catch (\Exception $e) {
            return $this->errorResponse('Lỗi khi xóa đơn vị', 500);
        }
    }
}
