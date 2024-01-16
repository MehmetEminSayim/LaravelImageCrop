<?php

use Imagecrop\Mehmeteminsayim\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('/ibase', [Controllers\ImageCropController::class, 'index']);
Route::post('/upload1', [Controllers\ImageCropController::class, 'upload'])->name("upload1");
