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
     * Get Origins
     *
     * @OA\Get(
     *     path="/api/origins",
     *     summary="Get all origins",
     *     tags={"Metadata"},
     *     @OA\Response(response=200, description="List of origins"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function origins(Request $request)
    {
        return response()->json(config('news.active_origins' , []));
    }

    /**
     * Get Sources
     *
     * @OA\Get(
     *     path="/api/sources",
     *     summary="Get unique blog sources",
     *     tags={"Metadata"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Filter sources by search keyword",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(response=200, description="List of unique sources"),
     *     security={{"bearerAuth":{}}}
     * )
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
     * Get Authors
     *
     * @OA\Get(
     *     path="/api/authors",
     *     summary="Get unique blog authors",
     *     tags={"Metadata"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Filter authors by search keyword",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(response=200, description="List of unique authors"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function authors(Request $request)
    {
        $query = Blog::selectRaw("jt.author")
            ->fromRaw("blogs, JSON_TABLE(authors, '$[*]' COLUMNS (author VARCHAR(255) PATH '$')) as jt")
            ->distinct();

        if ($request->has('search')) {
            $query->where("jt.author", "LIKE", "%" . $request->search . "%");
        }

        $authors = $query->paginate($request->get('per_page', 10));

        return response()->json($authors);
    }

    /**
     * Get Categories
     *
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get list of categories",
     *     tags={"Metadata"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Filter categories by name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(response=200, description="List of categories"),
     *     security={{"bearerAuth":{}}}
     * )
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
     *
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create a new category",
     *     tags={"Metadata"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Technology")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Category created successfully"),
     *     @OA\Response(response=422, description="Validation error"),
     *     security={{"bearerAuth":{}}}
     * )
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
     *
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Update an existing category",
     *     tags={"Metadata"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Science")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Category updated successfully"),
     *     @OA\Response(response=404, description="Category not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     security={{"bearerAuth":{}}}
     * )
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
     *
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Delete a category",
     *     tags={"Metadata"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Category deleted successfully"),
     *     @OA\Response(response=404, description="Category not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
