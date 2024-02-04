<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::all());
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    public function store(StoreRequest $request): CategoryResource
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = 'categories/' . Str::uuid() . '.' . $file->extension();
            $file->storePubliclyAs('public', $name);
            $data['photo'] = $name;
        }

        $category = Category::create($data);

        return new CategoryResource($category);
    }

    public function update(Category $category, StoreRequest $request): CategoryResource
    {
        $category->update($request->validated());

        return new CategoryResource($category);
    }

    public function destroy(Category $category): Response
    {
        $category->delete();

        return response()->noContent();
    }
}
