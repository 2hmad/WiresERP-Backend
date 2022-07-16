<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DebtsController;
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

    Route::post('categories', [CategoriesController::class, 'categories']);
    Route::post('sub-categories', [CategoriesController::class, 'sub_categories']);
    Route::post('add-category', [CategoriesController::class, 'addCategory']);
    Route::post('add-subcategory', [CategoriesController::class, 'addSubCategory']);
    Route::post('edit-category', [CategoriesController::class, 'editCategory']);
    Route::post('edit-subcategory', [CategoriesController::class, 'editSubCategory']);
    Route::post('delete-category', [CategoriesController::class, 'deleteCategory']);
    Route::post('delete-subcategory', [CategoriesController::class, 'deleteSubCategory']);

    Route::post('products', [ProductsController::class, 'products']);
    Route::post('add-product', [ProductsController::class, 'addProduct']);

    Route::post('clients', [DebtsController::class, 'clients']);
    Route::post('add-client', [DebtsController::class, 'addClient']);
    Route::post('suppliers', [DebtsController::class, 'suppliers']);
    Route::post('add-supplier', [DebtsController::class, 'addSupplier']);
});
