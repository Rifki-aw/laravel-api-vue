<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ambil token dari authorization pada header, sesuai dengan user-api.json
        $token = $request->header('Authorization');
        $authenticate = true;

        // pastikan tokennya ada
        if (!$token) {
            $authenticate = false;
        }

        // pastikan toen yang ada berasal dari DB
        $user = User::where('token', $token)->first();
        if (!$user) {
            $authenticate = false;
        }

        Auth::login($user);

        // jika terautentikasi tokennya
        if ($authenticate) {
            return next($request);
        } else {
            return response()->json([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
