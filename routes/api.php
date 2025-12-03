<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\InvoiceAdminController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Feature;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/* ===========================
   AUTH
   =========================== */

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');


/* ===========================
   CURRENT USER + FEATURES (Dynamic)
   =========================== */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

    $user = $request->user();

    // 1) List ALL dynamic feature names
    $featureNames = DB::table('features')
        ->select('name')
        ->distinct()
        ->orderBy('name')
        ->pluck('name');

    // 2) Check each feature for the current user
    $features = [];
    foreach ($featureNames as $name) {
        $features[$name] = Feature::for($user)->active($name);
    }

    return [
        'user'     => $user,
        'features' => $features,
    ];
});


/* ===========================
   USER INVOICES (Users can see ALL invoices)
   =========================== */

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
});


/* ===========================
   ADMIN ROUTES (Feature Manager, User Manager, Invoice Manager)
   =========================== */

Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {

    /* ---- Dynamic Features ---- */
    Route::get('/features', [FeatureController::class, 'index']);              // list feature names
    Route::post('/features/create', [FeatureController::class, 'createFeature']);
    Route::post('/features/activate', [FeatureController::class, 'activate']);
    Route::post('/features/deactivate', [FeatureController::class, 'deactivate']);

    /* ---- User Management ---- */
    Route::get('/admin/users', [UserAdminController::class, 'index']);
    Route::post('/admin/users', [UserAdminController::class, 'store']);
    Route::post('/admin/users/{user}/toggle-admin', [UserAdminController::class, 'toggleAdmin']);

    /* ---- Invoice Management (Admin) ---- */
    Route::get('/admin/invoices', [InvoiceAdminController::class, 'index']);
    Route::post('/admin/invoices', [InvoiceAdminController::class, 'store']);
});


/* ===========================
   FEATURE-GATED EXAMPLE
   =========================== */
Route::middleware('auth:sanctum')->get('/new-dashboard-data', function (Request $request) {
    if (! Feature::for($request->user())->active('new-dashboard')) {
        return response()->json(['message' => 'Feature disabled'], 403);
    }

    return [
        'stats' => [10, 20, 30],
        'message' => 'You have access to the new dashboard!',
    ];
});
