<?php

use Illuminate\Http\Request;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */


Route::any('/test', 'TestController@test');
Route::any('/', 'HomeController@index');





Route::group(['middleware' => ['api', 'checkToken', 'api.guestauth']], function ($router) {

    Route::any('/vuely/{file}', function($file){
        return file_get_contents('http://reactify.theironnetwork.org/data/vuely/' . $file);
    });
   
    Route::prefix('callcenter')->group(function ($router) {
        Route::any('dialto.xml', 'TwilioCallController@responseCall');
    });



    Route::prefix('/geocoding')->group(function ($router) {
        Route::get('reverse', 'GeocodingController@reverse');
        Route::get('search', 'GeocodingController@search');
        Route::get('staticmap', 'GeocodingController@staticMap');
    });

    Route::resource('/countries', 'CountriesController');
    Route::resource('/cities', 'CitiesController');
    Route::resource('/states', 'StatesController');
    Route::resource('/pages', 'PagesController');
    Route::get('/pages/slug/{slug}', 'PagesController@getBySlug');
    Route::get('/load', 'HomeController@load');
    Route::get('/stats', 'HomeController@stats');
    Route::get('/users-online', 'HomeController@usersOnline');

    Route::prefix('auth')->group(function ($router) {
        Route::post('/register', 'AuthController@register')->name('api.auth.register');
        Route::post('/validate', 'AuthController@validateRegistration')->name('api.auth.validate-register');
        Route::post('/verify', 'AuthController@verify')->name('api.auth.verify');
        Route::post('/resend-code', 'AuthController@resendCode')->name('api.auth.resend-code');
        Route::post('/login', 'AuthController@login')->name('api.auth.login');
        Route::post('/logout', 'AuthController@logout')->name('api.auth.logout');
        Route::post('/forgot-password', 'AuthController@forgotPassword')->name('api.auth.forgot-password');
        Route::post('/reset-password', 'AuthController@resetPassword')->name('api.auth.reset-password');
        Route::any('/login-guest', 'AuthController@loginAsGuest')->name('api.auth.login-guest');
    });


    //appointments
    Route::prefix('appointments')->group(function ($router) {
        Route::get('/available-cities/{countryId?}', 'AppointmentsController@getAvailableCities')->name('api.appointments.available-cities');
    });

    Route::get('/profiles/{id}', 'ProfilesController@show');
    Route::get('/profiles/{id}/reviews', 'ProfilesController@reviews');
    Route::get('/profiles/{id}/prices', 'ProfilesController@prices');
    Route::get('/galleries/{id}', 'GalleryController@show');


    //providers
    Route::prefix('providers')->group(function ($router) {
        Route::get('/', 'ProvidersController@index')->name('api.providers.index');
        Route::get('/{id}/availability', 'ProvidersController@getAvailablity');
    });

    Route::group(['middleware' => ['auth:api']], function ($router) {
        Route::resource('/users', 'UsersController');

        Route::prefix('push')->group(function ($router) {
            Route::post('/register', 'PushController@registerPushToken');
            Route::delete('/remove', 'PushController@removePushToken');
        });


        Route::get('/appointments/stats', 'AppointmentsController@stats');
        Route::resource('/appointments', 'AppointmentsController');
        Route::post('/appointments/{id}/rate', 'AppointmentsController@rate');
        Route::get('/appointments/pending/{userId}', 'AppointmentsController@getPendingAppointments');

        Route::any('/positions', 'PositionsController@updatePosition');
        Route::get('/positions/list', 'PositionsController@getPositions');
        Route::get('/positions/distance/{userId}', 'PositionsController@calculateDistance');


        Route::prefix('notifications')->group(function ($router) {
            Route::get('/', 'NotificationsController@all');
            Route::get('/unread', 'ProfilesController@unread');
            Route::get('/read', 'ProfilesController@read');
        });


        Route::prefix('callcenter')->group(function ($router) {
            Route::post('make-call', 'TwilioCallController@makeCall');
        });

        Route::prefix('profiles')->group(function ($router) {
            Route::put('/{id}', 'ProfilesController@update');
            Route::put('/{id}/prices', 'ProfilesController@updatePrices');
            Route::put('/{id}/status', 'ProfilesController@updateStatus');
            Route::put('/{id}/notifications', 'ProfilesController@updateNotifications');
            Route::get('/{id}/position', 'ProfilesController@getUserPosition');

            //restricted areas
            Route::get('/{id}/restricted-areas', 'ProfilesController@listBlockedZones');
            Route::post('/{id}/restricted-areas', 'ProfilesController@addBlockedZone');
            Route::put('/{id}/restricted-areas/{zoneId}', 'ProfilesController@updateBlockedZone');
            Route::delete('/{id}/restricted-areas/{zoneId}', 'ProfilesController@removeBlockedZone');
            Route::get('/{id}/restricted-areas/{zoneId}', 'ProfilesController@getBlockedZone');

            //identity verifications
            Route::get('/{id}/identity-verification', 'ProfilesController@getIdentityData');
            Route::put('/{id}/identity-verification', 'ProfilesController@updateIdentity');
        });


        Route::prefix('galleries')->group(function ($router) {

            Route::post('/{id}', 'GalleryController@update');
            Route::delete('/{id}/delete-files', 'GalleryController@deleteMultiple');
            Route::delete('/{id}/{file}', 'GalleryController@delete');
        });
        Route::prefix('auth')->group(function ($router) {
            Route::get('/me', 'AuthController@me')->name('api.auth.me');
        });
        Route::prefix('security')->group(function ($router) {
            Route::post('/change-password', 'SecurityController@changePassword')->name('api.security.change-password');
        });


        Route::prefix('referers')->group(function ($router) {
            Route::get('/', 'ReferersController@index');
            Route::get('/{id}', 'ReferersController@getReservationsOfRefered');
        });


        //chat
        Route::prefix('chat')->namespace('Chat')->group(function ($router) {
            Route::get('/test', 'ConversationsController@test')->name('api.chat.conversations.test');
            Route::get('/', 'ConversationsController@getMyConversations')->name('api.chat.conversations.get-my-conversations');
            Route::get('/user/{id}', 'ConversationsController@getUserConversations')->name('api.chat.conversations.get-user-conversations');
            Route::get('/listChats', 'ConversationsController@listChats')->name('api.chat.conversations.list-conversations');
            Route::post('/', 'ConversationsController@initConversation');
            Route::post('/{id}/read', 'ConversationsController@markAsReaded')->name('api.chat.conversations.mark-as-readed');
            Route::get('/{id}/messages', 'ConversationsController@readMessages')->name('api.chat.conversations.read-messages');
            Route::post('/{id}/messages', 'ConversationsController@sendMessage')->name('api.chat.conversations.send-message');
            Route::get('/{id}/appointment', 'ConversationsController@getAppointment')->name('api.chat.conversations.get-appointment');
            Route::get('/{id}', 'ConversationsController@getById')->name('api.chat.conversations.get-conversations');
            Route::delete('/{id}', 'ConversationsController@delete')->name('api.chat.conversations.clear-conversation');
        });

        Route::prefix('admin')->namespace('Admin')->group(function ($router) {
            Route::get('/providers/all', 'ProvidersController@all');
            Route::get('/providers/{id}/prices', 'ProvidersController@getPrices');
            Route::get('/providers/{id}/refereds', 'ProvidersController@refereds');
            Route::put('/providers/{id}/security', 'ProvidersController@updateSecurity');
            Route::post('/providers/{id}/identity', 'ProvidersController@saveIdentity');
            Route::get('/providers/{id}/gallery', 'ProvidersController@getGalleryImage');
            Route::post('/providers/{id}/gallery', 'ProvidersController@saveGalleryImage');
            Route::delete('/providers/{id}/gallery/{galleryId}', 'ProvidersController@removeGalleryImage');

            Route::resource('/providers', 'ProvidersController');

            Route::get('/users/all', 'UsersController@all');
            Route::put('/users/{id}/security', 'UsersController@updateSecurity');
            Route::get  ('/users/{id}/refereds', 'UsersController@refereds');
            Route::resource('/users', 'UsersController');

            Route::get('/reservations/unchecked', 'ReservationsController@getUnchecked');
            Route::resource('/reservations', 'ReservationsController');
            Route::resource('/automessages', 'AutoMessagesController');
            Route::resource('/pages', 'PagesController');

            Route::get('/currencies', 'CurrenciesController@show');
            Route::put('/currencies', 'CurrenciesController@update');
            Route::post('/push/send', 'PushNotificationsController@send');

            Route::get('/settings', 'SettingsController@index');
            Route::put('/settings', 'SettingsController@save');

            Route::get('/referers', 'ReferersController@index');
            Route::get('/referers/{id}', 'ReferersController@updatePayment');
        });
    });
});
