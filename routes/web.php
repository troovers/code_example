<?php

Route::group(['middleware' => 'connect'], function() {

    Route::group(['middleware' => ['auth', 'impersonating']], function() {

        // Add routes for the app (users)
        require_once('app/web.php');

        Route::group(['middleware' => 'admin_access'], function() {
            Route::get('/configurations', 'Configurations\Configurations@index');
            Route::post('/configurations/save', 'Configurations\Configurations@save');
        });
    });
});
