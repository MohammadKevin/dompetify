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
     * Display a listing of categories (global defaults + user's custom categories).
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        $query = Category::forUserOrGlobal($userId);

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
     * Store a newly created category for the user.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['user_id'] = $request->user()->id;
        }

        $category = Category::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dibuat.',
            'data' => new CategoryResource($category),
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show(Request $request, Category $category): JsonResponse
    {
        $this->authorizeCategoryAccess($request, $category);

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
        $this->authorizeCategoryOwnership($request, $category);

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
    public function destroy(Request $request, Category $category): JsonResponse
    {
        $this->authorizeCategoryOwnership($request, $category);

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }

    /**
     * Check if user can view category (either system global or user's own).
     */
    protected function authorizeCategoryAccess(Request $request, Category $category): void
    {
        $user = $request->user();
        if ($user && $category->user_id && $category->user_id !== $user->id) {
            abort(403, 'Akses tidak diizinkan ke kategori pengguna lain.');
        }
    }

    /**
     * Check if user can edit/delete category (cannot edit system globals without user_id unless admin).
     */
    protected function authorizeCategoryOwnership(Request $request, Category $category): void
    {
        $user = $request->user();
        if ($user && $category->user_id && $category->user_id !== $user->id) {
            abort(403, 'Akses tidak diizinkan mengubah kategori pengguna lain.');
        }
    }
}
