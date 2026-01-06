<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\YiiApiClient;

// =========================
//  IMPORT CONTROLLER YANG BENAR
// =========================

// PUBLIC dataset controller
use App\Http\Controllers\DatasetController as PublicDatasetController;

// ADMIN controllers
use App\Http\Controllers\Admin\DatasetController; 
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminProfileController;

/*
|--------------------------------------------------------------------------
| PUBLIC PAGES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    /** @var YiiApiClient $yii */
    $yii = app(YiiApiClient::class);
    $res = $yii->get('/api/categories');

    $items = [];
    if (($res['success'] ?? false) && is_array($res['data']['data'] ?? null)) {
        $items = $res['data']['data'];
    }

    $categories = collect($items)->map(function ($row) {
        return (object) $row;
    });

    return view('datasets.home', compact('categories'));
})->name('home');
Route::get('/profil-bsn', fn() => view('datasets.profil'))->name('profil');
Route::get('/tentang-bsn', fn() => view('datasets.tentang'))->name('tentang');

/*
|--------------------------------------------------------------------------
| PUBLIC DATASET (Frontend)
|--------------------------------------------------------------------------
*/
Route::get('/data', [PublicDatasetController::class, 'publicIndex'])
    ->name('datasets.public');

Route::get('/data/{id}', [PublicDatasetController::class, 'showPublic'])
    ->name('datasets.detail');

Route::get('/data/view/{id}', [PublicDatasetController::class, 'viewFile'])
    ->name('datasets.viewFile');

Route::get('/data/download/{id}', [PublicDatasetController::class, 'downloadFile'])
    ->name('datasets.downloadFile');

/*
|--------------------------------------------------------------------------
| LOGIN PAGE
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    if (session('yii_api_token') && session('yii_user')) return redirect()->route('admin.dashboard');
    return view('datasets.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| LOGIN SUBMIT (CUSTOM LOGIN)
|--------------------------------------------------------------------------
*/
Route::post('/login', function (Request $request) {

    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    /** @var YiiApiClient $yii */
    $yii = app(YiiApiClient::class);
    $res = $yii->postJson('/api/auth/login', [
        'email' => $request->email,
        'password' => $request->password,
    ]);

    if (!($res['success'] ?? false)) {
        $error = 'Email atau password salah.';

        if (!empty($res['error'])) {
            $error = (string) $res['error'];
        } elseif (is_array($res['data'] ?? null) && !empty($res['data']['error'])) {
            $error = (string) $res['data']['error'];
        } elseif (!empty($res['raw'])) {
            $error = 'Login gagal. Response backend: ' . (string) $res['raw'];
        }

        return back()->withErrors([
            'email' => $error,
        ])->withInput();
    }

    $token = $res['data']['access_token'] ?? null;
    $yiiUser = $res['data']['user'] ?? null;
    if (!$token || !is_array($yiiUser)) {
        return back()->withErrors([
            'email' => 'Login gagal. Response backend tidak valid.',
        ])->withInput();
    }

    session([
        'is_logged_in' => true,
        'admin_id'     => $yiiUser['id'] ?? null,
        'yii_api_token' => $token,
        'yii_user' => $yiiUser,
    ]);

    return redirect()->route('admin.dashboard');
})->name('login.submit');

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/logout', function () {
    Auth::logout();
    session()->flush();
    return redirect()->route('home');
})->name('logout');

    // route khusus superadmin
Route::prefix('admin')->name('admin.')->middleware(['auth.custom', 'role:superadmin'])->group(function () {

    // Approve dataset
    Route::post('/datasets/{id}/approve', [DatasetController::class, 'approve'])
        ->name('datasets.approve');

    // Manajemen user (hapus/block admin nakal)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/freeze', [AdminUserController::class, 'freeze'])->name('users.freeze');
    Route::patch('/users/{user}/unfreeze', [AdminUserController::class, 'unfreeze'])->name('users.unfreeze');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Halaman Rekapan User (statistik per admin)
    Route::get('/rekapan-user', [AdminDashboardController::class, 'rekapanUser'])
        ->name('dashboard.rekapanUser');

    // Manajemen kategori (hanya superadmin)
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
});
/*
|--------------------------------------------------------------------------
| ADMIN AREA (PROTECTED)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware('auth.custom')->group(function () {

    // dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // Dashboard summary API (JSON) untuk frontend
    Route::get('/dashboard-summary', [AdminDashboardController::class, 'apiSummary'])
        ->name('dashboard.summary');

    // profil admin (user yang sedang login)
    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

    // Daftar dataset yang sudah di-approve (view only) - harus didefinisikan SEBELUM resource
    Route::get('/datasets/approved', [DatasetController::class, 'approvedIndex'])
        ->name('datasets.approved');

    // ADMIN DATASET CRUD — ini pakai controller admin
    Route::resource('/datasets', DatasetController::class);

    Route::get('/users/{id}', [AdminUserController::class, 'show'])
        ->name('users.show');

    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
        ->name('users.destroy');
});
