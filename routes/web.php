<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PembayaranOrangTuaController;
use App\Http\Controllers\PembayaranAdminController;
use App\Http\Controllers\KategoriTagihanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Auth;


// =====================================================
// LOGIN
// =====================================================

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [
    AuthController::class,
    'showLogin'
])->name('login');

Route::post('/login', [
    AuthController::class,
    'login'
])->name('login.process');


// =====================================================
// LOGOUT
// =====================================================

Route::post('/logout', [
    AuthController::class,
    'logout'
])
->middleware('auth')
->name('logout');


// =====================================================
// ADMIN
// =====================================================

Route::middleware([
    'auth',
    'role:admin'
])
->prefix('admin')
->group(function () {


    // =================================================
    // DASHBOARD ADMIN
    // =================================================

    Route::get('/dashboard', [
        DashboardController::class,
        'admin'
    ])->name('admin.dashboard');

// =================================================
// PROFIL ADMIN
// =================================================

Route::get('/profil', function () {
    return view('admin.profil.index');
})->name('admin.profil');

Route::get('/profil/edit', function () {
    return view('admin.profil.edit');
})->name('admin.profil.edit');

Route::put('/profil', [
    AuthController::class,
    'updateProfil'
])->name('admin.profil.update');

    // =================================================
    // DATA SISWA
    // =================================================

    Route::resource(
        'siswa',
        SiswaController::class
    )->names([
        'index'   => 'admin.siswa.index',
        'create'  => 'admin.siswa.create',
        'store'   => 'admin.siswa.store',
        'show'    => 'admin.siswa.show',
        'edit'    => 'admin.siswa.edit',
        'update'  => 'admin.siswa.update',
        'destroy' => 'admin.siswa.destroy',
    ]);


    // =================================================
    // DATA ORANG TUA
    // =================================================

    Route::resource(
        'orang-tua',
        OrangTuaController::class
    )->names([
        'index'   => 'admin.orang-tua.index',
        'create'  => 'admin.orang-tua.create',
        'store'   => 'admin.orang-tua.store',
        'show'    => 'admin.orang-tua.show',
        'edit'    => 'admin.orang-tua.edit',
        'update'  => 'admin.orang-tua.update',
        'destroy' => 'admin.orang-tua.destroy',
    ]);


    // =================================================
    // DATA KELAS
    // =================================================

    Route::resource(
        'kelas',
        KelasController::class
    )->names([
        'index'   => 'admin.kelas.index',
        'create'  => 'admin.kelas.create',
        'store'   => 'admin.kelas.store',
        'edit'    => 'admin.kelas.edit',
        'update'  => 'admin.kelas.update',
        'destroy' => 'admin.kelas.destroy',
    ]);


    // =================================================
    // DATA TAGIHAN
    // =================================================

    Route::resource(
        'tagihan',
        TagihanController::class
    )->only([
        'index',
        'create',
        'store',
        'show',
        'edit',
        'update',
        'destroy'
    ])->names([
        'index'   => 'admin.tagihan.index',
        'create'  => 'admin.tagihan.create',
        'store'   => 'admin.tagihan.store',
        'show'    => 'admin.tagihan.show',
        'edit'    => 'admin.tagihan.edit',
        'update'  => 'admin.tagihan.update',
        'destroy' => 'admin.tagihan.destroy',
    ]);


    // =================================================
    // PEMBAYARAN ADMIN
    // =================================================

    Route::get(
        '/pembayaran',
        [
            PembayaranAdminController::class,
            'index'
        ]
    )->name('admin.pembayaran.index');


    // =================================================
// LAPORAN
// =================================================

Route::get(
    '/laporan',
    [
        LaporanController::class,
        'index'
    ]
)->name('admin.laporan.index');


Route::get(
    '/laporan/pdf',
    [
        LaporanController::class,
        'pdf'
    ]
)->name('admin.laporan.pdf');


Route::get(
    '/laporan/excel',
    [
        LaporanController::class,
        'excel'
    ]
)->name('admin.laporan.excel');


    // =================================================
    // NOTIFIKASI PEMBAYARAN
    // =================================================

    Route::get(
        '/pembayaran/notifikasi',
        [
            PembayaranAdminController::class,
            'notifikasi'
        ]
    )->name('admin.pembayaran.notifikasi');


    // =================================================
    // SETUJUI PEMBAYARAN
    // =================================================

    Route::patch(
        '/pembayaran/{pembayaran}/setujui',
        [
            PembayaranAdminController::class,
            'setujui'
        ]
    )->name('admin.pembayaran.setujui');


    // =================================================
    // TOLAK PEMBAYARAN
    // =================================================

    Route::patch(
        '/pembayaran/{pembayaran}/tolak',
        [
            PembayaranAdminController::class,
            'tolak'
        ]
    )->name('admin.pembayaran.tolak');


    // =================================================
    // KATEGORI TAGIHAN
    // =================================================

    Route::get(
        '/kategori',
        [
            KategoriTagihanController::class,
            'index'
        ]
    )->name('admin.kategori');


    Route::post(
        '/kategori',
        [
            KategoriTagihanController::class,
            'store'
        ]
    )->name('admin.kategori.store');


    Route::delete(
        '/kategori/{kategori}',
        [
            KategoriTagihanController::class,
            'destroy'
        ]
    )->name('admin.kategori.destroy');

});


// =====================================================
// ORANG TUA
// =====================================================

Route::middleware([
    'auth',
    'role:orang_tua'
])
->prefix('orangtua')
->group(function () {


    // =================================================
    // DASHBOARD ORANG TUA
    // =================================================

    Route::get(
        '/dashboard',
        [
            DashboardController::class,
            'orangTua'
        ]
    )->name('orangtua.dashboard');


    // =================================================
    // HALAMAN PEMBAYARAN
    // =================================================

    Route::get(
        '/pembayaran',
        [
            PembayaranOrangTuaController::class,
            'index'
        ]
    )->name('orangtua.pembayaran.index');


    // =================================================
    // FORM BAYAR TAGIHAN
    // =================================================

    Route::get(
        '/pembayaran/{tagihan}/bayar',
        [
            PembayaranOrangTuaController::class,
            'create'
        ]
    )->name('orangtua.pembayaran.create');


    // =================================================
    // PROSES KIRIM PEMBAYARAN
    // =================================================

    Route::post(
        '/pembayaran/{tagihan}',
        [
            PembayaranOrangTuaController::class,
            'store'
        ]
    )->name('orangtua.pembayaran.store');

});