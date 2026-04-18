<?php

namespace App\Http\Controllers\API;

use App\Helpers\CreateSlugHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\FilterDataTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use FilterDataTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $mode = $request->input('mode');

        if (!isset($mode) || empty($mode) || (isset($mode) && $mode !== 'list')) {
            return $this->successResponse(CategoryResource::collection(Category::all()->where('is_active', '1')));
        }

        $perPage = $request->input('per_page', 10);
        $filters = $request->input('filters', []);

        $query = Category::query();
        $query = $this->applyFilters($query, $filters, Category::FILTERS);
        $categories = $query->paginate($perPage);

        $data = [
            'header_filter' => Category::getLangKey(),
            'data' => CategoryResource::collection($categories),
            'pagination' => [
                'total' => $categories->total(),
                'per_page' => $categories->perPage(),
                'current_page' => $categories->currentPage(),
            ],
        ];

        return $this->successResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        //
        $data = $request->all();
        $data['slug'] = $this->checkAndCreateSlug($data);

        $category = Category::create($data);

        return $this->successResponse(new CategoryResource($category), 'Tạo danh mục thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        return $this->successResponse(new CategoryResource(Category::findOrFail($id)));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryStoreRequest $request, string $id)
    {
        //
        try {
            $category = Category::findOrFail($id);

            $data = $request->all();
            $data['slug'] = $this->checkAndCreateSlug($data);

            $category->update($data);

             return $this->successResponse(new CategoryResource($category), 'Cập nhật thành công');
        } catch (\Throwable $th) {
            return $this->errorResponse('Cập nhật danh mục thất bại', 404, $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return $this->noContentResponse();
        } catch (\Throwable $th) {
            return $this->errorResponse('Xóa danh mục thất bại', 404);
        }
    }

    public function checkAndCreateSlug($request)
    {
        $data = $request;
        $slug = isset($data['slug']) && !empty($data['slug']) ? $data['slug'] : $data['name'];

        return CreateSlugHelper::createSlug($slug);
    }
}
