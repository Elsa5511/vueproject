<?php

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('admin.login');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/forgot-password', 'Auth\LoginController@showForgotForm')->name('admin.forgot-password');


Route::group(['middleware' => ['admin', 'admin.auth', 'admin.menu']], function () {
    Route::match(array('GET', 'POST'), '/logout', 'DashboardController@logout')->name('admin.logout');
    Route::any('/', 'DashboardController@index')->name('admin.home');

    Route::resource('/pages', 'PagesController', ['as' => 'admin']);
    Route::resource('/users', 'UsersController', ['as' => 'admin']);
    Route::get('/users/{id}/profile', 'UsersController@editProfile')->name('admin.users.profile');
    Route::put('/users/{id}/profile', 'UsersController@updateProfile')->name('admin.users.profile-store');
    Route::get('/users/{id}/security', 'UsersController@editSecurity')->name('admin.users.security');
    Route::put('/users/{id}/security', 'UsersController@updateSecurity')->name('admin.users.security-store');
    Route::get('/users/{id}/appointments', 'UsersController@listAppointments')->name('admin.users.appointments');
    Route::get('/users/{id}/appointments/{appointmentId}', 'UsersController@showAppointment')->name('admin.users.appointments.detail');
    Route::put('/users/{id}/appointments', 'UsersController@updateAppointments')->name('admin.users.appointments-store');


    Route::resource('/providers', 'ProvidersController', ['as' => 'admin']);
    Route::get('/providers/{id}/profile', 'ProvidersController@editProfile')->name('admin.providers.profile');
    Route::put('/providers/{id}/profile', 'ProvidersController@updateProfile')->name('admin.providers.profile-store');
    Route::get('/providers/{id}/security', 'ProvidersController@editSecurity')->name('admin.providers.security');
    Route::put('/providers/{id}/security', 'ProvidersController@updateSecurity')->name('admin.providers.security-store');
    Route::get('/providers/{id}/appointments', 'ProvidersController@listAppointments')->name('admin.providers.appointments');
    Route::get('/providers/{id}/appointments/{appointmentId}', 'ProvidersController@showAppointment')->name('admin.providers.appointments.detail');
    Route::put('/providers/{id}/appointments', 'ProvidersController@updateAppointments')->name('admin.providers.appointments-store');
    Route::get('/providers/{id}/prices', 'ProvidersController@showPrices')->name('admin.providers.prices');
    Route::put('/providers/{id}/prices', 'ProvidersController@updatePrices')->name('admin.providers.prices-store');
    Route::get('/providers/{id}/gallery', 'ProvidersController@showGallery')->name('admin.providers.gallery');
    Route::delete('/providers/delete-image/{galleryId}', 'ProvidersController@deleteImageGallery')->name('admin.providers.gallery-delete-image');
    Route::post('/providers/{id}/gallery', 'ProvidersController@updateGallery')->name('admin.providers.gallery-store');
    Route::get('/providers/{id}/identity', 'ProvidersController@showIdentity')->name('admin.providers.identity');
    Route::put('/providers/{id}/identity', 'ProvidersController@showIdentity')->name('admin.providers.identity-store');

    Route::get('/appointments', 'AppointmentsController@index')->name('admin.appointments');
    Route::get('/appointments/{id}', 'AppointmentsController@show')->name('admin.appointments.show');

    Route::get('/maps/providers', 'MapsController@showProvidersMap')->name('admin.maps.providers');

    Route::get('/currencies', 'CurrenciesController@show')->name('admin.currencies');
    Route::put('/currencies', 'CurrenciesController@update')->name('admin.currencies.update');

    Route::resource('/automessages', 'AutoMessagesController', ['as' => 'admin']);

    Route::resource('/chats', 'ChatsController', ['as' => 'admin']);

    Route::get('/settings', 'SettingsController@index')->name('admin.settings');
    Route::post('/settings', 'SettingsController@save')->name('admin.settings.save');
    Route::get('/clear-cache', 'SettingsController@clearCache')->name('admin.artisan.cache-clear');
});

