<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TravelOrderController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/travel-orders', [TravelOrderController::class, 'store']);
    Route::get('/travel-orders', [TravelOrderController::class, 'index']);
    Route::get('/travel-orders/{id}', [TravelOrderController::class, 'show']);
    Route::patch('/travel-orders/{id}/status', [TravelOrderController::class, 'updateStatus']);
    Route::delete('/travel-orders/{id}', [TravelOrderController::class, 'destroy']);

});

Route::post('/login', function (Request $request) {

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token
    ]);
});