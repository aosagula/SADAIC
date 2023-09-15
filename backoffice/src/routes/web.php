<?php

use Illuminate\Support\Facades\Route;
use App\Models\ProfileUpdates;

Route::post('/auth', 'AuthController@auth');
Route::get('/php_info', function(){
    phpinfo();
      
});

Route::get('/xdebug', function () {
    xdebug_info();
});
Route::get('/works/files', 'WorksController@downloadFile')->name('works.file');
Route::middleware(['guest'])->group(function () {
    // Routes accessible without authentication
    Route::get('/login', 'AuthController@login')->name('login');
    
    
});




Route::middleware(['web','auth'])->group(function () {
    Route::get('/', 'DashboardController@index');

    Route::get('/members', 'MembersController@index');
    Route::get('/members/datatables', 'MembersController@datatables');
    Route::get('/members/{registration}', 'MembersController@view');
    Route::post('/members/{registration}/status', 'MembersController@changeStatus');

    Route::get('/profiles', 'ProfilesController@index');
    Route::get('/profiles/datatables', 'ProfilesController@datatables');
    Route::get('/profiles/{profile}', 'ProfilesController@view');
    Route::post('/profiles/{profile}/status', 'ProfilesController@changeStatus');

    Route::get('/works', 'WorksController@index');
    Route::get('/works/datatables', 'WorksController@datatables');

    Route::get('/works/{registration}', 'WorksController@showView');
    Route::post('/works/{registration}/status', 'WorksController@changeStatus');
    Route::post('/works/{registration}/response', 'WorksController@response');
    Route::post('/works/{registration}/observations', 'WorksController@saveObservations');

    Route::get('/jingles', 'JinglesController@index');
    Route::get('/jingles/datatables', 'JinglesController@datatables');
    Route::get('/jingles/{registration}', 'JinglesController@showView');
    Route::post('/jingles/{registration}/status', 'JinglesController@changeStatus');
    Route::post('/jingles/{registration}/response', 'JinglesController@response');
    Route::post('/jingles/{registration}/observations', 'JinglesController@saveObservations');

    Route::get('/integration', 'IntegrationController@index');
    Route::get('/integration/works', 'IntegrationController@exportWorks');
    Route::get('/integration/jingles', 'IntegrationController@exportJingles');
    Route::get('/integration/members', 'IntegrationController@exportMembers');
    Route::post('/integration/works', 'IntegrationController@importWorks');


    Route::get('/logout', 'AuthController@logout')->name('logout');
});