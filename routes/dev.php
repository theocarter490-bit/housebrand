<?php

use App\Http\Controllers\DevController;
use Illuminate\Support\Facades\Route;

Route::prefix('dev')->group(function () {
    Route::get('/product-admin-to-manufac', [DevController::class, 'productAdminToManufac']);
    Route::get('/module-per-set', [DevController::class, 'modulePerSet']);
    Route::get('/color-palette-setup', [DevController::class, 'colorPaletteSetup']);
    Route::get('/role-perm-key-update', [DevController::class, 'rolePermissionUpdate']);
    Route::get('/project-erase', [DevController::class, 'projectEarse']);
    Route::get('/user-signup-source-update', [DevController::class, 'signUpSourceUpdate']);
    Route::get('/set-def-col-theme/{userId}', [DevController::class, 'setColorTheme'])->name('set.def.color.theme');
    Route::get('/set-default-section-content-data', [DevController::class, 'setSectionData'])->name('setSectionData');

    Route::get('/set-designer-customer-assignment',[DevController::class,'setDesignerCustomerAssignment']);

    Route::get('/mood-board', [DevController::class, 'moodBoard'])->name('moodBoard');
    Route::post('/mood-board', [DevController::class, 'save'])->name('moodboard.save');
    
    Route::get('/shop-slider-style', [DevController::class, 'sliderStyle'])->name('sliderStyle');

});
