<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return CategoryResource::collection(
            $request->user()->categories()->get()
        );
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $request->user()->categories()->create($request->validated());

        return new CategoryResource($category);
    }

    public function update(StoreCategoryRequest $request, Category $category)
    {
        $this->authorizeOwner($category, $request);

        $category->update($request->validated());

        return new CategoryResource($category);
    }

    public function destroy(Request $request, Category $category)
    {
        $this->authorizeOwner($category, $request);

        $category->delete();

        return response()->noContent();
    }

    private function authorizeOwner(Category $category, Request $request): void
    {
        abort_if($category->user_id !== $request->user()->id, 403);
    }
}
