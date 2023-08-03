<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

/**
 * Rutas nuevo sistema
 */
Route::get('/', function() {
    if (!Auth::user()) {
        return redirect('/login');
    } else {
        if (Auth::user()->type == 'user') {
            return redirect('/user');
        } elseif (Auth::user()->type == 'member') {
            return redirect('/member');
        }
    }
})->middleware(['auth:web,members']);

Route::get('/logout', 'Auth\LoginController@logout');

Route::get('/agency', 'AgenciesController@index');

Route::get('/member', 'MemberController@index');
Route::prefix('member')->group(function() {
  Route::get('/jingles/{registration}/response', 'JinglesController@showResponse')->where([ 'registration' => '[0-9]+' ]);
  Route::post('/jingles/{registration}/response', 'JinglesController@response')->where([ 'registration' => '[0-9]+' ]);
  Route::resource('jingles', 'JinglesController');

  Route::get('/password/update', 'Member\PasswordController@showUpdate');
  Route::post('/password/update', 'Member\PasswordController@update');

  Route::get('/profile/list', 'Member\ProfileController@showList');
  Route::get('/profile/update', 'Member\ProfileController@showUpdate');
  Route::post('/profile/update', 'Member\ProfileController@update');
  Route::get('/profile/update/{update}/view', 'Member\ProfileController@viewUpdate')
  ->where([ 'update' => '[0-9]+' ]);

  Route::get('/work/register', 'Member\WorkController@showRegister');
  Route::post('/work/register', 'Member\WorkController@register');
  Route::post('/work/save', 'Member\WorkController@saveRegister');
  Route::get('/work/result', 'Member\WorkController@showResult');
  Route::post('/work/search_author', 'Member\WorkController@searchAuthor');
  Route::get('/work/list', 'Member\WorkController@showList');
  Route::get('/work/edit/{registration}', 'Member\WorkController@showEdit')
  ->where([ 'registration' => '[0-9]+' ]);
  Route::get('/work/view/{registration}', 'Member\WorkController@showView')
  ->where([ 'registration' => '[0-9]+' ]);
  Route::get('/work/delete/{registration}', 'Member\WorkController@deleteRegistration')
  ->where([ 'registration' => '[0-9]+' ]);

  Route::post('/work/distribution/delete', 'Member\WorkController@deleteDistribution');

  Route::get('/work/{registration}/response', 'Member\WorkController@showResponse')
  ->where([ 'registration' => '[0-9]+' ]);
  Route::post('/work/{registration}/response', 'Member\WorkController@response')
  ->where([ 'registration' => '[0-9]+' ]);

  /**
   * Rutas que invocan embeds
   */
  Route::get('/international-performance/report', 'MemberController@showInternationalPerformanceReport');
  Route::get('/payment-letters', 'MemberController@showPaymentLetters');
  Route::get('/payment-orders', 'MemberController@showPaymentOrders');
  Route::get('/payment-request', 'MemberController@showPaymentRequest');
  Route::get('/performance/report', 'MemberController@showPerformanceReport');
  Route::get('/status', 'MemberController@showStatus');
  Route::get('/work/list-sadaic', 'MemberController@showWorkList');
  Route::get('/work/edit/{registration}', 'Member\WorkController@showEdit')
  ->where([ 'registration' => '[0-9]+' ]);
});

Route::get('/user', 'UserController@index');

Route::prefix('user')->group(function() {
  Route::get('/jingles/{registration}/response', 'JinglesController@showResponse')->where([ 'registration' => '[0-9]+' ]);
  Route::post('/jingles/{registration}/response', 'JinglesController@response')->where([ 'registration' => '[0-9]+' ]);
  Route::resource('jingles', 'JinglesController');

  Route::get('/member/register', 'User\MemberController@showRegister');
  Route::post('/member/register', 'User\MemberController@register');
  Route::get('/member/password', 'User\MemberController@showPassword');
  Route::post('/member/password', 'User\MemberController@password');
  Route::get('/member/profile', 'User\MemberController@showProfile');
  Route::post('/member/profile', 'User\MemberController@profile');
  Route::get('/member/list', 'User\MemberController@showList');
  Route::get('/member/edit/{registration}', 'User\MemberController@showEdit')
  ->where([ 'registration' => '[0-9]+' ]);
  Route::post('/member/edit/{registration}', 'User\MemberController@edit')
  ->where([ 'registration' => '[0-9]+' ]);
  Route::get('/member/{registration}', 'User\MemberController@show')
  ->where([ 'registration' => '[0-9]+' ]);
  Route::get('/work/delete/{registration}', 'User\WorkController@deleteRegistration')
  ->where([ 'registration' => '[0-9]+' ]);

  Route::get('/work/register', 'User\WorkController@showRegister');
  Route::post('/work/register', 'User\WorkController@register');
  Route::post('/work/save', 'User\WorkController@saveRegister');
  Route::get('/work/result', 'User\WorkController@showResult');
  Route::post('/work/search_author', 'User\WorkController@searchAuthor');
  Route::get('/work/list', 'User\WorkController@showList');
  Route::get('/work/edit/{registration}', 'User\WorkController@showEdit')
  ->where([ 'registration' => '[0-9]+' ]);
  Route::get('/work/view/{registration}', 'User\WorkController@showView')
  ->where([ 'registration' => '[0-9]+' ]);

  Route::post('/work/distribution/delete', 'User\WorkController@deleteDistribution');
});

/**
 * Rutas de integración con SADAIC
 */
Route::post('/sadaic/login/{member_id}-{heir}', 'SADAICController@login')
->where([
  'member_id' => '[0-9]+',
  'heir'      => '[0-9]'
]);
Route::get('/sadaic/embed', 'SADAICController@embed');
Route::post('/sadaic/submit', 'SADAICController@submit');

/**
 * Rutas de manejo de archivos adjuntos
 */
Route::get('/files/download/{file}', 'FileController@downloadFile')
->where([
  'file' => '[0-9]+'
]);
Route::post('/files/delete/{file}', 'FileController@deleteFile')
->where([
  'file' => '[0-9]+'
]);
Route::post('/files/upload', 'FileController@uploadFile');
