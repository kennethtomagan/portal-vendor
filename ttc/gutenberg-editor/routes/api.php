<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Shared\GutenbergEditorController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

// Route::get('/', function (Request $request) {
//     //
// });

Route::get('/', [GutenbergEditorController::class, 'index'])->name('gutenbergeditor.index');
Route::get('/{id}', 'App\Http\Controllers\Shared\GutenbergEditorController@show');
Route::get('/edit/{id}', 'App\Http\Controllers\Shared\GutenbergEditorController@edit');
Route::post('/edit/{id}', 'App\Http\Controllers\Shared\GutenbergEditorController@save');
