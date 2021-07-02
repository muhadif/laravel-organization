<?php

use App\Http\Controllers\Admin\Organization\Member\MemberController;
use App\Http\Controllers\Admin\Organization\OrganizationController;
use App\Http\Controllers\Admin\Role\RoleController;
use App\Http\Controllers\Admin\User\UserController;
use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('organizations', OrganizationController::class);
    Route::name('organizations.')->prefix('organizations/{organization}')->group(function (){
        Route::resource('members', MemberController::class)->only([
            'show', 'edit', 'update', 'create', 'store', 'destroy'
        ]);;
    });

    Route::get('organizations.search', [OrganizationController::class, 'index']);

});
