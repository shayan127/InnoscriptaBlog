<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BlogRequest;
use App\Http\Requests\SearchBlogRequest;
use App\Services\Elasticsearch\ElasticsearchService;
use App\Models\Blog;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;

/**
 * @OA\Schema(
 *     schema="BlogRequest",
 *     required={"title", "content", "url", "source", "authors"},
 *     @OA\Property(property="title", type="string", maxLength=255, example="New blog"),
 *     @OA\Property(property="snippet", type="string", maxLength=500, nullable=true, example="New blog content..."),
 *     @OA\Property(property="content", type="string", example="New blog content ..."),
 *     @OA\Property(property="url", type="string", format="url", maxLength=500, example="https://example.com/blog/new-blog"),
 *     @OA\Property(property="published_at", type="string", format="date-time", nullable=true, example="2024-02-25T12:00:00Z"),
 *     @OA\Property(property="source", type="string", maxLength=255, example="The New York Times"),
 *     @OA\Property(property="origin", type="string", maxLength=255, nullable=true, example="NYT"),
 *     @OA\Property(
 *         property="authors",
 *         type="array",
 *         @OA\Items(type="string", maxLength=255, example="Shayan")
 *     ),
 *     @OA\Property(property="image", type="string", format="url", maxLength=500, nullable=true, example="https://example.com/images/1.jpg"),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         nullable=true,
 *         @OA\Items(type="integer", example=1)
 *     )
 * )
 */
class BlogController extends Controller
{
    protected $elasticsearch;

    public function __construct(ElasticsearchService $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    /**
     * Create a new Blog.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/api/blogs",
     *     summary="Create a Blog",
     *     tags={"Blogs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BlogRequest")
     *     ),
     *     @OA\Response(response="201", description="Blog created successfully"),
     *     @OA\Response(response="422", description="Validation error")
     * )
     */
    public function store(BlogRequest $request)
    {
        $blog = Blog::create($request->validated());
        $blog->categories()->attach($request->categories);
        $blog->updated_at = null; // to trigger observer
        $blog->save();


        return response()->json([
            'message' => 'Blog created successfully.',
            'blog'    => $blog
        ], Response::HTTP_CREATED);
    }

    /**
     * Update an existing Blog.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/api/blogs/{id}",
     *     summary="Update a Blog",
     *     tags={"Blogs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         example=1
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BlogRequest")
     *     ),
     *     @OA\Response(response="200", description="Blog updated successfully"),
     *     @OA\Response(response="404", description="Blog not found")
     * )
     */
    public function update(BlogRequest $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $blog->categories()->attach($request->categories);
        $blog->update($request->validated());

        return response()->json([
            'message' => 'Blog updated successfully.',
            'blog'    => $blog
        ]);
    }

    /**
     * Delete a Blog.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/api/blogs/{id}",
     *     summary="Delete a Blog",
     *     tags={"Blogs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         example=1
     *     ),
     *     @OA\Response(response="200", description="Blog deleted successfully"),
     *     @OA\Response(response="404", description="Blog not found")
     * )
     */
    public function delete($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully.']);
    }

    /**
     * Search for Blogs.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/blogs/search",
     *     summary="Search for blogs",
     *     tags={"Blogs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         example="apple"
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         example="2024-02-25"
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         example="Technology"
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         example="The New York Times"
     *     ),
     *     @OA\Response(response="200", description="Search results returned successfully"),
     *     @OA\Response(response="422", description="Validation error")
     * )
     */
    public function search(SearchBlogRequest $request)
    {
        // todo
    }
}
