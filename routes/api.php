<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
 
use App\Http\Controllers\AppControllers\BuildingUnitController;

Route::get('/home', [BuildingUnitController::class, 'homePage']);

Route::get('/building_units/{id}', [BuildingUnitController::class, 'specificBuildingUnits']);
