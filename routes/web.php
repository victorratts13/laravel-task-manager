<?php

use App\Http\Controllers\BackEndController;
use App\Livewire\Home;
use Illuminate\Support\Facades\Route;

Route::get('/',     Home::class)->name('home');
Route::get('/supervisor', [BackEndController::class, 'Supervisor']);