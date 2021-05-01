<?php

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

Route::get('/', 'HomeController');
Route::group(['middleware' => ['guest:api']], function () {
    Route::post('login', 'Auth\LoginController@login');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');
    Route::get('/user', 'Settings\ProfileController@index');
    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');
    Route::group(['prefix' => 'sample'], function () {
        Route::get('/get-data', 'SampleController@getData');
        Route::post('/add', 'SampleController@store');
        Route::get('/edit/{nomor_sampel}', 'SampleController@show');
        Route::delete('/delete/{sampel}', 'SampleController@destroy');
        Route::post('/update/{sampel}', 'SampleController@update');
        Route::get('/get-sample/{nomor_sampel}', 'SampleController@getSampel');
    });
    Route::apiResource('pengguna', 'PenggunaController');
    Route::apiResource('dinkes', 'FasyankesController');
    Route::apiResource('kota', 'KotaController');
    Route::get('roles-option', 'OptionController@getRoles');
    Route::get('lab-pcr-option', 'OptionController@getLabPCR');
    Route::get('jenis-sampel-option', 'OptionController@getJenisSampel');
    Route::get('jenis-vtm', 'OptionController@getJenisVTM');
    Route::get('validator-option', 'OptionController@getValidator');
    Route::get('negara-option', 'OptionController@getNegara');
    Route::get('/provinsi', 'OptionController@listProvinsi');
    Route::get('/kecamatan', 'OptionController@listKecamatan');
    Route::get('/kelurahan', 'OptionController@listKelurahan');
    Route::group(['prefix' => 'registrasi-mandiri'], function () {
        Route::get('/', 'RegistrasiMandiri@getData');
        Route::get('/export-excel', 'RegistrasiMandiri@exportMandiri')->middleware('can:isAdmin');
    });

    Route::group(['prefix' => 'registrasi-rujukan'], function () {
        Route::post('/cek', 'RegistrasiRujukanController@cekData');
        Route::post('/store', 'RegistrasiRujukanController@store');
        Route::get('/export-excel-rujukan', 'RegistrasiMandiri@exportRujukan')->middleware('can:isAdmin');
        Route::delete('/delete/{register}', 'RegistrasiRujukanController@destroy');
        Route::post('update/{register}/{pasien}', 'RegistrasiRujukanController@update');
        Route::get('update/{register}/{pasien}', 'RegistrasiRujukanController@show');
    });
    //V1
    Route::group(['namespace' => 'V1', 'prefix' => 'v1'], function () {
        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/notifications', 'NotificationController');
        });

        Route::group(['prefix' => 'ekstraksi'], function () {
            Route::get('/detail/{sampel}', 'EkstraksiController@detail');
            Route::post('/edit/{sampel}', 'EkstraksiController@edit');
            Route::post('/set-invalid/{sampel}', 'EkstraksiController@setInvalid');
            Route::post('/set-proses/{sampel}', 'EkstraksiController@setProses');
            Route::post('/terima', 'EkstraksiController@terima');
            Route::post('/kirim', 'EkstraksiController@kirim');
            Route::post('/kirim-ulang', 'EkstraksiController@kirimUlang');
            Route::post('/musnahkan/{sampel}', 'EkstraksiController@musnahkan');
            Route::post('/set-kurang/{sampel}', 'EkstraksiController@setKurang');
            Route::post('/set-swab-ulang/{sampel}', 'EkstraksiController@setSwabUlang');
        });

        Route::group(['prefix' => 'pcr'], function () {
            Route::get('/detail/{id}', 'PCRController@detail');
            Route::post('/edit/{id}', 'PCRController@edit');
            Route::post('/terima', 'PCRController@terima');
            Route::post('/invalid/{id}', 'PCRController@invalid');
            Route::post('/input/{id}', 'PCRController@input');
            Route::post('/musnahkan/{id}', 'PCRController@musnahkan');
            Route::post('/import-hasil-pemeriksaan', 'ImportPemeriksaanSampelController@importHasilPemeriksaan');
            Route::post('/import-data-hasil-pemeriksaan', 'ImportPemeriksaanSampelController@importDataHasilPemeriksaan');
        });

        Route::group(['prefix' => 'sampel'], function () {
            Route::get('/cek-nomor-sampel', 'CekNomorSampelController');
        });

        Route::get('list-kota-jabar', 'KotaController@listKota');
        Route::get('list-kota-all', 'KotaController@listKotaAll');
        Route::get('kota/detail/{kota}', 'KotaController@show');
        Route::get('list-fasyankes-jabar', 'FasyankesController@listByProvinsi');
        Route::get('fasyankes/detail/{fasyankes}', 'FasyankesController@show');
        Route::group(['prefix' => 'register'], function () {
            Route::post('mandiri', 'RegisterController@storeMandiri');
            Route::post('mandiri/update/{register}/{pasien}', 'RegisterController@storeUpdate');
            Route::get('mandiri/{register_id}/{pasien_id}', 'RegisterController@getById');
            Route::get('logs/{register_id}', 'RegisterController@logs');
            Route::delete('mandiri/{register}/{pasien}', 'RegisterController@delete');
            Route::get('noreg', 'RegisterController@requestNomor');

            Route::post('import-mandiri', 'ImportRegisterController@importRegisterMandiri');
            Route::post('import-rujukan', 'ImportRegisterController@importRegisterRujukan');
            Route::post('import-rujukan-tes-masif', 'ImportRegisterController@importRegisterRujukanTesMasif');
            Route::post('import-data-rujukan', 'ImportRegisterController@importDataRegisterRujukan');
            Route::post('import-data-tes-masif', 'ImportRegisterController@importDataRegisterRujukan');
        });

        Route::group(['prefix' => 'verifikasi', 'middleware' => 'can:isAdminVerifikator'], function () {
            Route::get('list', 'NotVerifiedController@index');
            Route::get('export-excel-verifikasi', 'NotVerifiedController@export');
            Route::get('list-verified', 'VerifiedController@index');
            Route::get('export-excel-terverifikasi', 'VerifiedController@export');
            Route::get('detail/{sampel}', 'VerifikasiController@show');
            Route::post('edit-status-sampel/{sampel}', 'VerifikasiController@updateToVerified');
            Route::post('verifikasi-single-sampel/{sampel}', 'VerifikasiController@verifiedSingleSampel');
            Route::post('verifikasi-bulk-sampel', 'VerifikasiController@verifiedBulkSampel');
        });

        Route::group(['prefix' => 'verifikasi'], function () {
            Route::get('list-kategori', 'VerifikasiController@listKategori');
        });

        Route::group(['prefix' => 'validasi', 'middleware' => 'can:isAdminValidator'], function () {
            Route::get('/', 'ValidasiController@index');
            Route::get('/export', 'ValidasiController@export');
            Route::get('list-validator', 'ValidasiController@getValidator');
            Route::post('bulk-validasi', 'ValidasiController@bulkValidate');
            Route::post('edit-status-sampel/{sampel}', 'ValidasiController@updateToValidate');
            Route::get('detail/{sampel}', 'ValidasiController@show');
            Route::post('reject-sample/{sampel}', 'ValidasiController@rejectSample');
            Route::get('export-pdf/{sampel}', 'ExportPDFController@downloadPdfHasil');
            Route::post('regenerate-pdf/{sampel}', 'ExportPDFController@regeneratePdfHasil');
        });

        Route::group(['prefix' => 'pelacakan-sampel'], function () {
            Route::get('list', 'PelacakanSampelController@index');
            Route::get('detail/{sampel}', 'PelacakanSampelController@show');
        });
        //pelaporan
        Route::group(['prefix' => 'pelaporan'], function () {
            Route::get('data', 'PelaporanController@fetchData');
        });

        Route::group(['prefix' => 'list'], function () {
            Route::get('reagen-ekstraksi', 'ReagenEkstraksiListController');
            Route::get('reagen-pcr', 'ReagenPCRListController');
        });
        Route::group(['middleware' => 'can:isAdmin'], function () {
            Route::apiResource('jenis-vtm', 'JenisVTMController');
            Route::apiResource('reagen-ekstraksi', 'ReagenEkstraksiController');
            Route::apiResource('reagen-pcr', 'ReagenPCRController');
        });
        Route::group(['prefix' => 'tes-masif'], function () {
            Route::get('/', 'TesMasifController@index');
            Route::post('/registering', 'TesMasifController@bulk');
        });

        Route::prefix('swab-antigen')->group(function () {
            Route::get('/nomor-registrasi', 'SwabAntigenController@getNomorRegistrasi');
            Route::post('/bulk', 'SwabAntigenController@bulkValidasi');
            Route::post('/import', 'SwabAntigenController@import');
            Route::get('/export-excel', 'SwabAntigenController@export');
            Route::get('/export-pdf/{swab_antigen}', 'ExportPDFController@downloadPdfSwabAntigen');
            Route::post('/regenerate-pdf/{swab_antigen}', 'ExportPDFController@regeneratePdfSwabAntigen');
        });

        Route::apiResource('swab-antigen', 'SwabAntigenController');
        Route::get('download', 'FileDownloadController');
    });
    //V2
    Route::group(['namespace' => 'V2', 'prefix' => 'v2'], function () {
        Route::group(['prefix' => 'ekstraksi'], function () {
            Route::get('/get-data', 'EkstraksiController@getData');
        });

        Route::group(['prefix' => 'pcr'], function () {
            Route::get('/get-data', 'PCRController@index');
        });

        Route::group(['prefix' => 'pelacakan-sampel'], function () {
            Route::get('list', 'PelacakanSampelController@index');
        });

        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/tracking-progress', 'DashboardController@trackingProgress');
            Route::get('/positif-negatif', 'DashboardController@positifNegatif');
            Route::get('/pasien-diperiksa', 'DashboardController@pasienDiperiksa');
            Route::get('/registrasi', 'DashboardController@registrasi');
            Route::get('/admin-sampel', 'DashboardController@adminSampel');
            Route::get('/ekstraksi', 'DashboardController@ekstraksi');
            Route::get('/pcr', 'DashboardController@pcr');
            Route::get('/verifikasi', 'DashboardController@verifikasi');
            Route::get('/validasi', 'DashboardController@validasi');
            Route::group(['prefix' => 'list'], function () {
                Route::get('/sampel-perdomisili', 'SampelPerDomisili');
            });
        });

        Route::group(['prefix' => 'chart'], function () {
            Route::get('/regis', 'DashboardChartController@registrasi');
            Route::get('/sampel', 'DashboardChartController@sampel');
            Route::get('/ekstraksi', 'DashboardChartController@ekstraksi');
            Route::get('/pcr', 'DashboardChartController@pcr');
            Route::get('/positif-negatif', 'DashboardChartController@positifNegatif');
        });
    });
});

Route::group(['prefix' => 'v1/tes-masif', 'namespace' => 'V1'], function () {
    Route::post('/bulk', 'StoreTesMasifController');
});
