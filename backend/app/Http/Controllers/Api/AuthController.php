<?php

namespace App\Http\Controllers\Api;


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
class AuthController extends Controller
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

}
