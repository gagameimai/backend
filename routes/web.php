<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 後台路由
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'admin/filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

Route::group([
    'namespace' => 'Admin',
    // 'prefix' => 'admin'
], function () {
    // 登入
    Route::get('/', 'LoginController@index')->name('admin.login');
    Route::get('login', 'LoginController@index')->name('admin.login');
    Route::post('login', 'LoginController@loginAuth');

    Route::group(['middleware' => 'role.auth:admin'],function () {
        // 首頁
        Route::prefix('main')->group(function () {
            Route::get('/', 'MainController@index')->name('admin.main');
        });

        // 登出
        Route::get('logout', 'MainController@logout')->name('admin.logout');

        Route::group(['namespace' => 'Setting'],function () {
            // 網站基本惡定
            Route::prefix('website')->group(function () {
                Route::get('/', 'WebsiteInfoController@index')->name('admin.website');
                Route::get('all', 'WebsiteInfoController@all');
                Route::patch('/', 'WebsiteInfoController@update');
            });

            // 網站基本惡定
            Route::prefix('qa')->group(function () {
                Route::get('/', 'QaController@index')->name('admin.qa');
                Route::get('all', 'QaController@all');
                Route::patch('/', 'QaController@update');
            });

            // 關於我們
            Route::prefix('about')->group(function () {
                Route::get('/', 'AboutController@index')->name('admin.about');
                Route::get('all', 'AboutController@all');
                Route::patch('/', 'AboutController@update');
            });
        });

        // banner
        Route::prefix('banner')->group(function () {
            Route::get('/', 'BannerController@index')->name('admin.banner');
            Route::post('/', 'BannerController@create');

            Route::prefix('all')->group(function () {
                Route::get('/', 'BannerController@all');
                Route::patch('sort', 'BannerController@sort');
            });

            Route::prefix('{id}')->group(function () {
                Route::get('/', 'BannerController@find');
                Route::patch('/', 'BannerController@update');
                Route::delete('/', 'BannerController@delete');
                Route::patch('status', 'BannerController@status');
            });
        });

        // 首頁精選商品
        Route::prefix('recommend_product')->group(function () {
            Route::get('/', 'RecommendProductController@index')->name('admin.recommend_product');
            Route::post('/', 'RecommendProductController@create');
            Route::get('options', 'RecommendProductController@options');

            Route::prefix('all')->group(function () {
                Route::get('/', 'RecommendProductController@all');
                Route::patch('sort', 'RecommendProductController@sort');
            });

            Route::prefix('{id}')->group(function () {
                Route::get('/', 'RecommendProductController@find');
                Route::patch('/', 'RecommendProductController@update');
                Route::delete('/', 'RecommendProductController@delete');
                Route::patch('status', 'RecommendProductController@status');
            });
        });

        // 安裝案例
        Route::prefix('install_case')->group(function () {
            Route::get('/', 'InstallCaseController@index')->name('admin.install_case');
            Route::post('/', 'InstallCaseController@create');

            Route::prefix('all')->group(function () {
                Route::get('/', 'InstallCaseController@all');
                Route::patch('sort', 'InstallCaseController@sort');
            });

            Route::prefix('{id}')->group(function () {
                Route::get('/', 'InstallCaseController@find');
                Route::patch('/', 'InstallCaseController@update');
                Route::delete('/', 'InstallCaseController@delete');
                Route::patch('status', 'InstallCaseController@status');
                Route::patch('pinned', 'InstallCaseController@pinned');
                Route::patch('home', 'InstallCaseController@home');
            });
        });

        // 資源管理
        Route::group(['namespace' => 'Resource'],function () {
            Route::prefix('resource_category')->group(function () {
                Route::get('/', 'ResourceCategoryController@index')->name('admin.resource_category');
                Route::post('/', 'ResourceCategoryController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'ResourceCategoryController@all');
                    Route::patch('sort', 'ResourceCategoryController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'ResourceCategoryController@find');
                    Route::patch('/', 'ResourceCategoryController@update');
                    Route::delete('/', 'ResourceCategoryController@delete');
                    Route::patch('status', 'ResourceCategoryController@status');
                });
            });

            Route::prefix('resource')->group(function () {
                Route::get('/', 'ResourceController@index')->name('admin.resource');
                Route::post('/', 'ResourceController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'ResourceController@all');
                    Route::patch('sort', 'ResourceController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'ResourceController@find');
                    Route::patch('/', 'ResourceController@update');
                    Route::delete('/', 'ResourceController@delete');
                    Route::patch('status', 'ResourceController@status');
                });
            });
        });

        // dealer
        Route::prefix('dealer')->group(function () {
            Route::get('/', 'DealerController@index')->name('admin.dealer');
            Route::post('/', 'DealerController@create');

            Route::prefix('all')->group(function () {
                Route::get('/', 'DealerController@all');
                Route::patch('sort', 'DealerController@sort');
            });

            Route::prefix('{id}')->group(function () {
                Route::get('/', 'DealerController@find');
                Route::patch('/', 'DealerController@update');
                Route::delete('/', 'DealerController@delete');
                Route::patch('status', 'DealerController@status');
            });
        });

        // 產品管理
        Route::group(['namespace' => 'Product'],function () {
            Route::prefix('car_brand')->group(function () {
                Route::get('/', 'CarBrandController@index')->name('admin.car_brand');
                Route::post('/', 'CarBrandController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarBrandController@all');
                    Route::patch('sort', 'CarBrandController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarBrandController@find');
                    Route::patch('/', 'CarBrandController@update');
                    Route::delete('/', 'CarBrandController@delete');
                    Route::patch('status', 'CarBrandController@status');
                });
            });

            Route::prefix('car')->group(function () {
                Route::get('/', 'CarController@index')->name('admin.car');
                Route::post('/', 'CarController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarController@all');
                    Route::patch('sort', 'CarController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarController@find');
                    Route::patch('/', 'CarController@update');
                    Route::delete('/', 'CarController@delete');
                    Route::patch('status', 'CarController@status');
                });
            });

            Route::prefix('car_frame')->group(function () {
                Route::get('/', 'CarFrameController@index')->name('admin.car_frame');
                Route::post('/', 'CarFrameController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarFrameController@all');
                    Route::patch('sort', 'CarFrameController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarFrameController@find');
                    Route::patch('/', 'CarFrameController@update');
                    Route::delete('/', 'CarFrameController@delete');
                    Route::patch('status', 'CarFrameController@status');
                    Route::patch('img', 'CarFrameController@deleteImg');
                });
            });

            Route::prefix('car_blind_spot')->group(function () {
                Route::get('/', 'CarBlindSpotController@index')->name('admin.car_blind_spot');
                Route::post('/', 'CarBlindSpotController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarBlindSpotController@all');
                    Route::patch('sort', 'CarBlindSpotController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarBlindSpotController@find');
                    Route::patch('/', 'CarBlindSpotController@update');
                    Route::delete('/', 'CarBlindSpotController@delete');
                    Route::patch('status', 'CarBlindSpotController@status');
                    Route::patch('top', 'CarBlindSpotController@top');
                    Route::patch('spc', 'CarBlindSpotController@spc');
                });
            });

            Route::prefix('car_blind_spot_format/{car_blind_spot?}')->group(function () {
                Route::get('/', 'CarBlindSpotFormatController@index')->name('admin.car_blind_spot_format');
                Route::post('/', 'CarBlindSpotFormatController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarBlindSpotFormatController@all');
                    Route::patch('sort', 'CarBlindSpotFormatController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarBlindSpotFormatController@find');
                    Route::patch('/', 'CarBlindSpotFormatController@update');
                    Route::delete('/', 'CarBlindSpotFormatController@delete');
                    Route::patch('status', 'CarBlindSpotFormatController@status');
                });
            });

            Route::prefix('car_fitting')->group(function () {
                Route::get('/', 'CarFittingController@index')->name('admin.car_fitting');
                Route::post('/', 'CarFittingController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarFittingController@all');
                    Route::patch('sort', 'CarFittingController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarFittingController@find');
                    Route::patch('/', 'CarFittingController@update');
                    Route::delete('/', 'CarFittingController@delete');
                    Route::patch('status', 'CarFittingController@status');
                    Route::patch('top', 'CarFittingController@top');
                });
            });

            Route::prefix('car_dashcam')->group(function () {
                Route::get('/', 'CarDashcamController@index')->name('admin.car_dashcam');
                Route::post('/', 'CarDashcamController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarDashcamController@all');
                    Route::patch('sort', 'CarDashcamController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarDashcamController@find');
                    Route::patch('/', 'CarDashcamController@update');
                    Route::delete('/', 'CarDashcamController@delete');
                    Route::patch('status', 'CarDashcamController@status');
                    Route::patch('top', 'CarDashcamController@top');
                });
            });

            Route::prefix('car_camera')->group(function () {
                Route::get('/', 'CarCameraController@index')->name('admin.car_camera');
                Route::post('/', 'CarCameraController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarCameraController@all');
                    Route::patch('sort', 'CarCameraController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarCameraController@find');
                    Route::patch('/', 'CarCameraController@update');
                    Route::delete('/', 'CarCameraController@delete');
                    Route::patch('status', 'CarCameraController@status');
                    Route::patch('top', 'CarCameraController@top');
                });
            });

            Route::prefix('car_headrest')->group(function () {
                Route::get('/', 'CarHeadrestController@index')->name('admin.car_headrest');
                Route::post('/', 'CarHeadrestController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarHeadrestController@all');
                    Route::patch('sort', 'CarHeadrestController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarHeadrestController@find');
                    Route::patch('/', 'CarHeadrestController@update');
                    Route::delete('/', 'CarHeadrestController@delete');
                    Route::patch('status', 'CarHeadrestController@status');
                    Route::patch('top', 'CarHeadrestController@top');
                });
            });

            Route::prefix('car_portable')->group(function () {
                Route::get('/', 'CarPortableController@index')->name('admin.car_portable');
                Route::post('/', 'CarPortableController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarPortableController@all');
                    Route::patch('sort', 'CarPortableController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarPortableController@find');
                    Route::patch('/', 'CarPortableController@update');
                    Route::delete('/', 'CarPortableController@delete');
                    Route::patch('status', 'CarPortableController@status');
                    Route::patch('top', 'CarPortableController@top');
                });
            });

            Route::prefix('car_audio_accessories')->group(function () {
                Route::get('/', 'CarAudioAccessoriesController@index')->name('admin.car_audio_accessories');
                Route::post('/', 'CarAudioAccessoriesController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarAudioAccessoriesController@all');
                    Route::patch('sort', 'CarAudioAccessoriesController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarAudioAccessoriesController@find');
                    Route::patch('/', 'CarAudioAccessoriesController@update');
                    Route::delete('/', 'CarAudioAccessoriesController@delete');
                    Route::patch('status', 'CarAudioAccessoriesController@status');
                    Route::patch('top', 'CarAudioAccessoriesController@top');
                });
            });

            Route::prefix('car_head_unit')->group(function () {
                Route::get('/', 'CarHeadUnitController@index')->name('admin.car_head_unit');
                Route::post('/', 'CarHeadUnitController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarHeadUnitController@all');
                    Route::patch('sort', 'CarHeadUnitController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarHeadUnitController@find');
                    Route::patch('/', 'CarHeadUnitController@update');
                    Route::delete('/', 'CarHeadUnitController@delete');
                    Route::patch('status', 'CarHeadUnitController@status');
                    Route::patch('top', 'CarHeadUnitController@top');
                });
            });

            Route::prefix('car_media')->group(function () {
                Route::get('/', 'CarMediaController@index')->name('admin.car_media');
                Route::post('/', 'CarMediaController@create');

                Route::prefix('all')->group(function () {
                    Route::get('/', 'CarMediaController@all');
                    Route::patch('sort', 'CarMediaController@sort');
                });

                Route::prefix('{id}')->group(function () {
                    Route::get('/', 'CarMediaController@find');
                    Route::patch('/', 'CarMediaController@update');
                    Route::delete('/', 'CarMediaController@delete');
                    Route::patch('status', 'CarMediaController@status');
                    Route::patch('top', 'CarMediaController@top');
                });
            });
        });
    });
});
