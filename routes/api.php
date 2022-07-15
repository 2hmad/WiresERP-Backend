<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\WarehousesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'uToken'], function () {
    Route::post('branches', [BranchesController::class, 'branches']);
    Route::post('add-branch', [BranchesController::class, 'addBranch']);
    Route::post('edit-branch', [BranchesController::class, 'editBranch']);
    Route::post('delete-branch', [BranchesController::class, 'deleteBranch']);

    Route::post('warehouses', [WarehousesController::class, 'warehouses']);
    Route::post('add-warehouse', [WarehousesController::class, 'addWarehouse']);
    Route::post('edit-warehouse', [WarehousesController::class, 'editWarehouse']);
    Route::post('delete-warehouse', [WarehousesController::class, 'deleteWarehouse']);
    Route::post('transfer-warehouses', [WarehousesController::class, 'transferWarehouses']);
    Route::post('transfer-warehouses', [WarehousesController::class, 'transferWarehouses']);
    Route::post('warehouse-inventory', [WarehousesController::class, 'warehouseInventory']);

    Route::post('products', [ProductsController::class, 'products']);
});
