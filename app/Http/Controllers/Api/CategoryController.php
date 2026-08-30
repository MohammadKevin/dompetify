<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::query();

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        $categories = $query->orderBy('type', 'asc')->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
            'meta' => [
                'total_categories' => $categories->count(),
                'expense_count' => $categories->where('type', 'EXPENSE')->count(),
                'income_count' => $categories->where('type', 'INCOME')->count(),
            ],
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dibuat.',
            'data' => new CategoryResource($category),
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui.',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}
