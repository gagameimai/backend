<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('question', 'QuestionController@get');
Route::get('about', 'AboutController@get');
Route::get('resource', 'ResourceController@get');
Route::get('partner', 'PartnerController@get');
Route::get('fitting', 'FittingController@get');
Route::get('fitting/{id}', 'FittingController@detail');
Route::get('dashcam', 'CarDashcamController@get');
Route::get('dashcam/{id}', 'CarDashcamController@detail');
Route::get('camera', 'CarCameraController@get');
Route::get('camera/{id}', 'CarCameraController@detail');
Route::get('headrest', 'CarHeadrestController@get');
Route::get('headrest/{id}', 'CarHeadrestController@detail');
Route::get('portable', 'CarPortableController@get');
Route::get('portable/{id}', 'CarPortableController@detail');
Route::get('audio_accessories', 'CarAudioAccessoriesController@get');
Route::get('audio_accessories/{id}', 'CarAudioAccessoriesController@detail');
Route::get('head_unit', 'CarHeadUnitController@get');
Route::get('head_unit/{id}', 'CarHeadUnitController@detail');
Route::get('blindspot', 'BlindSpotController@get');
Route::get('blindspot/{id}', 'BlindSpotController@detail');
Route::get('carframe', 'CarFrameController@get');
Route::get('carframe/{id}', 'CarFrameController@detail');
Route::get('multimedia', 'MultiMediaController@get');
Route::get('multimedia/{id}', 'MultiMediaController@detail');
Route::get('banner', 'BannerController@get');
Route::get('website', 'WebsiteController@get');
Route::get('search', 'SearchController@get');
Route::get('car', 'CarController@get');
Route::get('recommend_products', 'RecommendProductController@get');
Route::get('install_cases', 'InstallCaseController@get');
