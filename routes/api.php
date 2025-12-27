<?php

use App\Http\Controllers\RoverExecuteController;
use Illuminate\Support\Facades\Route;

Route::post('/rover/execute', RoverExecuteController::class);
