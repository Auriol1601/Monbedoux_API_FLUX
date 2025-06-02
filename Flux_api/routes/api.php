<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AddFluxController;

Route::post('/flux', [AddFluxController::class, 'addFlux']); 