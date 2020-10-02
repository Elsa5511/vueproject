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

Route::get('/resources/lang.json', 'LangController@index');
Route::get('/images/{section}/{folder}/{thumb}/{filename}', array('as' => 'imgs', 'uses' => 'ImagesController@dispatchImage'));
//Route::get('/downloads/resources/{uniqueId}', 'ResourceFilesController@download')->name('resources.download');


#Route::get('/', 'AngularController@serve');
#Route::get('{any?}', 'AngularController@serve')->where('any', '.*'); // this will ensure all routes will serve index.php file

