<?php

/**
 * App routes
 */

Route::get('/app/users', 'App\Users@index')->middleware('access:app/users,view');
Route::post('/app/users', 'App\Users@index')->middleware('access:app/users,view');
Route::get('/app/users/edit', 'App\Users@edit')->middleware('access:app/users,add');
Route::get('/app/users/edit/{id}', 'App\Users@edit')->middleware('access:app/users,edit');
Route::post('/app/users/save', 'App\Users@save')->middleware('access:app/users,[add.edit]');
Route::post('/app/users/delete', 'App\Users@delete')->middleware('access:app/users,delete');


/**
 * App agenda routes
 */


Route::get('/app/agenda', 'App\Agenda@index')->middleware('access:app/agenda,view');
Route::post('/app/agenda', 'App\Agenda@index')->middleware('access:app/agenda,view');
Route::get('/app/agenda/edit', 'App\Agenda@edit')->middleware('access:app/agenda,add');
Route::get('/app/agenda/edit/{id}', 'App\Agenda@edit')->middleware('access:app/agenda,edit');
Route::post('/app/agenda/save', 'App\Agenda@save')->middleware('access:app/agenda,[add.edit]');
Route::post('/app/agenda/delete', 'App\Agenda@delete')->middleware('access:app/agenda,delete');


/**
 * App news routes
 */


Route::get('/app/news', 'App\News@index')->middleware('access:app/news,view');
Route::post('/app/news', 'App\News@index')->middleware('access:app/news,view');
Route::get('/app/news/edit', 'App\News@edit')->middleware('access:app/news,add');
Route::get('/app/news/edit/{id}', 'App\News@edit')->middleware('access:app/news,edit');
Route::post('/app/news/save', 'App\News@save')->middleware('access:app/news,[add.edit]');
Route::post('/app/news/delete', 'App\News@delete')->middleware('access:app/news,delete');


/**
 * App polls routes
 */


Route::get('/app/polls', 'App\Polls@index')->middleware('access:app/pols,view');
Route::post('/app/polls', 'App\Polls@index')->middleware('access:app/pols,view');
Route::get('/app/polls/edit', 'App\Polls@edit')->middleware('access:app/pols,add');
Route::get('/app/polls/edit/{id}', 'App\Polls@edit')->middleware('access:app/pols,edit');
Route::post('/app/polls/save', 'App\Polls@save')->middleware('access:app/pols,[add.edit]');
Route::post('/app/polls/delete', 'App\Polls@delete')->middleware('access:app/pols,delete');


/**
 * App statistic routes
 */


Route::get('/app/stats', 'App\Stats@index')->middleware('access:app/stats,view');
Route::post('/app/stats', 'App\Stats@index')->middleware('access:app/stats,view');