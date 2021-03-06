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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('projects', 'projectsController');

    Route::post('/projects/{project}/tasks', 'projectTasksController@store');
    Route::post('/projects/{project}/invitations', 'ProjectInvitationsController@store');
    Route::patch('/projects/{project}/tasks/{task}', 'projectTasksController@update');


    Route::get('/home', 'HomeController@index')->name('home');
});



Auth::routes();
