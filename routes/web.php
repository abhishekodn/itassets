<?php

use App\Livewire\Assets\Index as AssetsIndex;
use App\Livewire\Assets\MyAssets;
use App\Livewire\Assets\Show as AssetShow;
use App\Livewire\Assignments\Index as AssignmentsIndex;
use App\Livewire\Categories\Index as CategoriesIndex;
use App\Livewire\Dashboard;
use App\Livewire\Departments\Index as DepartmentsIndex;
use App\Livewire\Locations\Index as LocationsIndex;
use App\Livewire\Maintenances\Index as MaintenancesIndex;
use App\Livewire\Reports\Depreciation;
use App\Livewire\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to(auth()->check() ? route('dashboard') : route('login'));
})->name('home');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    Route::get('my-assets', MyAssets::class)->name('my-assets');

    Route::middleware('permission:manage-departments')->group(function () {
        Route::get('departments', DepartmentsIndex::class)->name('departments.index');
    });

    Route::middleware('permission:manage-locations')->group(function () {
        Route::get('locations', LocationsIndex::class)->name('locations.index');
    });

    Route::middleware('permission:manage-categories')->group(function () {
        Route::get('categories', CategoriesIndex::class)->name('categories.index');
    });

    Route::middleware('permission:manage-assets')->group(function () {
        Route::get('assets', AssetsIndex::class)->name('assets.index');
        Route::get('assets/{asset}', AssetShow::class)->name('assets.show');
    });

    Route::middleware('permission:manage-assignments')->group(function () {
        Route::get('assignments', AssignmentsIndex::class)->name('assignments.index');
    });

    Route::middleware('permission:manage-maintenances')->group(function () {
        Route::get('maintenances', MaintenancesIndex::class)->name('maintenances.index');
    });

    Route::middleware('permission:view-reports')->group(function () {
        Route::get('reports/depreciation', Depreciation::class)->name('reports.depreciation');
    });

    Route::middleware('permission:manage-users')->group(function () {
        Route::get('users', UsersIndex::class)->name('users.index');
    });
});

require __DIR__.'/auth.php';
