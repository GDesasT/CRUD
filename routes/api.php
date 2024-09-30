<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Facades\JWTAuth;

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    $token = $user->createToken('token-name')->plainTextToken;

    return response()->json(['token' => $token]);
});

Route::post('/jwt/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    return response()->json(['token' => $token]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sanctum/posts', [PostController::class, 'index']);
    Route::post('/sanctum/posts', [PostController::class, 'store']);
    Route::get('/sanctum/posts/{id}', [PostController::class, 'show']);
    Route::put('/sanctum/posts/{id}', [PostController::class, 'update']);
    Route::delete('/sanctum/posts/{id}', [PostController::class, 'destroy']);
});

Route::middleware('auth:jwt')->group(function () {
    Route::get('/jwt/posts', [PostController::class, 'index']);
    Route::post('/jwt/posts', [PostController::class, 'store']);
    Route::get('/jwt/posts/{id}', [PostController::class, 'show']);
    Route::put('/jwt/posts/{id}', [PostController::class, 'update']);
    Route::delete('/jwt/posts/{id}', [PostController::class, 'destroy']);
});
