<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
});

// Turn Off Auth Routes Not Being Used
//Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/**
 * Tasks
 */
Route::resource('/task', 'TaskController');
Route::post('/updateAllPriorities', 'TaskController@updateAllPriorities');

/**
 * Projects
 */
Route::get('/project', 'ProjectController@index');
