<?php

use App\Http\Controllers\InternalApiV1Controller;
use App\Http\Middleware\ApiFirewall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(ApiFirewall::class)->prefix("/v1")->group(function () {
    Route::post("/pid",              [InternalApiV1Controller::class, 'pid']);
    Route::post("/monitor",          [InternalApiV1Controller::class, 'monitor']);
});
