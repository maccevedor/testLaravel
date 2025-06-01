<?php

use App\Infrastructure\Http\Controllers\ClientController;
use App\Infrastructure\Http\Controllers\PlanController;
use App\Infrastructure\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tenants', TenantController::class);
Route::apiResource('plans', PlanController::class);
Route::apiResource('clients', ClientController::class);
