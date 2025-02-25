<?php

namespace App\Http\Controllers\Api;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

/**
 * @OA\Info(
 *     title="Swagger with Laravel",
 *     version="1.0.0",
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Header(
 *      header="Accept",
 *      description="Accept header for the request",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *          enum={"application/json"}
 *      )
 *  )
 */
class UserController extends Controller
{

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/api/login",
     *     operationId="login",
     *     tags={"User"},
     *     summary="User login",
     *     description="User login and JWT token generation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="test@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login with JWT token",
     *     ),
     *     @OA\Response(response=401,description="Unauthorized"),
     * )
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/me",
     *     summary="Get logged-in user details",
     *     description="Returns details of the authenticated user.",
     *     tags={"User"},
     *     @OA\Response(
     *         response=200,
     *         description="User details retrieved successfully"
     *     ),
     *     @OA\Response(response=401,description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/api/refresh",
     *      summary="Refresh JWT token",
     *      description="Refreshes the authentication token for the logged-in user.",
     *      tags={"User"},
     *      @OA\Response(
     *          response=200,
     *          description="Token refreshed successfully",
     *      ),
     *      @OA\Response(response=401,description="Unauthorized"),
     *      security={{"bearerAuth":{}}}
     *  )
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Store a new user preference.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/user-preferences",
     *     summary="user preferences",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of preferences"),
     * )
     */
    public function getUserPreferences(Request $request)
    {
        $preferences = UserPreference::where('user_id', $request->user()->id)->get();

        return response()->json([
            'message'    => 'User preferences are ready.',
            'preferences' => $preferences
        ]);
    }

    /**
     * Store a new user preference.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/api/user-preferences",
     *     summary="Add a new user preference",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type", "value"},
     *             @OA\Property(property="type", type="string", example="theme"),
     *             @OA\Property(property="value", type="string", example="dark_mode")
     *         )
     *     ),
     *     @OA\Response(response="201", description="Preference created successfully"),
     *     @OA\Response(response="422", description="Validation error")
     * )
     */
    public function storeUserPreference(Request $request)
    {
        $request->validate([
            'type'  => 'required|in:category,source,author',
            'value' => 'required|string|max:255',
        ]);

        $preference = UserPreference::create([
            'user_id' => auth()->id(),
            'type'    => $request->type,
            'value'   => $request->value,
        ]);

        return response()->json([
            'message'    => 'User preference saved successfully.',
            'preference' => $preference
        ], Response::HTTP_CREATED);
    }

    /**
     * Delete a user preference.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/api/user-preferences/{id}",
     *     summary="Delete a user preference",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         example=1
     *     ),
     *     @OA\Response(response="200", description="Preference deleted successfully"),
     *     @OA\Response(response="404", description="Preference not found")
     * )
     */
    public function deleteUserPreference($id)
    {
        $preference = UserPreference::where('user_id', auth()->id())->findOrFail($id);
        $preference->delete();

        return response()->json(['message' => 'User preference deleted successfully.']);
    }
}
