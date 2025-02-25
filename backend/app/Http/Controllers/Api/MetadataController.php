<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class MetadataController extends Controller
{
    /**
     *   Get Sources
     */
    public function sources(Request $request)
    {
        $query = Blog::select('source')->distinct();

        if ($request->has('search')) {
            $query->where('source', 'LIKE', '%' . $request->search . '%');
        }

        $sources = $query->paginate($request->get('per_page', 10));

        return response()->json($sources);
    }

    /**
     *  Get Authors
     */
    public function authors(Request $request)
    {
        $query = Blog::selectRaw("JSON_UNQUOTE(JSON_EXTRACT(authors, '$[*]')) as author")
            ->distinct();

        if ($request->has('search')) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(authors, '$[*]')) LIKE ?", ['%' . $request->search . '%']);
        }

        $authors = $query->paginate($request->get('per_page', 10));

        return response()->json($authors);
    }

    /**
     *   Get Categories
     */
    public function categories(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $categories = $query->paginate($request->get('per_page', 10));

        return response()->json($categories);
    }

    /**
     * Create Category
     */
    public function createCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name|max:255',
        ]);

        $category = Category::create(['name' => $request->name]);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], Response::HTTP_CREATED);
    }

    /**
     * Update Category
     */
    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name|max:255',
        ]);

        $category = Category::findOrFail($id);
        $category->update(['name' => $request->name]);

        return response()->json(['message' => 'Category updated successfully', 'category' => $category]);
    }

    /**
     * Delete Category
     */
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
