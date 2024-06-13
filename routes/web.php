<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneralLedgersController;
use App\Http\Controllers\MstAccountCodesController;
use App\Http\Controllers\MstAccountTypesController;
use App\Http\Controllers\TransDataBankController;
use App\Http\Controllers\TransDataKasController;
use App\Http\Controllers\TransSalesController;
use App\Http\Controllers\TransPurchaseController;
use App\Http\Controllers\TransImportController;

//Route Login
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('auth/login', [AuthController::class, 'postlogin'])->name('postlogin')->middleware("throttle:5,2");

//Route Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //AccountType
    Route::get('/accounttype', [MstAccountTypesController::class, 'index'])->name('accounttype.index');
    Route::post('/accounttype', [MstAccountTypesController::class, 'index'])->name('accounttype.index');
    Route::post('accounttype/create', [MstAccountTypesController::class, 'store'])->name('accounttype.store');
    Route::get('accounttype/edit/{id}', [MstAccountTypesController::class, 'edit'])->name('accounttype.edit');
    Route::post('accounttype/update/{id}', [MstAccountTypesController::class, 'update'])->name('accounttype.update');
    Route::post('accounttype/activate/{id}', [MstAccountTypesController::class, 'activate'])->name('accounttype.activate');
    Route::post('accounttype/deactivate/{id}', [MstAccountTypesController::class, 'deactivate'])->name('accounttype.deactivate');
    Route::post('accounttype/delete/{id}', [MstAccountTypesController::class, 'delete'])->name('accounttype.delete');
    Route::post('accounttype/deleteselected', [MstAccountTypesController::class, 'deleteselected'])->name('accounttype.deleteselected');
    Route::post('accounttype/deactiveselected', [MstAccountTypesController::class, 'deactiveselected'])->name('accounttype.deactiveselected');
    
    //AccountCode
    Route::get('/accountcode', [MstAccountCodesController::class, 'index'])->name('accountcode.index');
    Route::post('/accountcode', [MstAccountCodesController::class, 'index'])->name('accountcode.index');
    Route::post('accountcode/create', [MstAccountCodesController::class, 'store'])->name('accountcode.store');
    Route::get('accountcode/edit/{id}', [MstAccountCodesController::class, 'edit'])->name('accountcode.edit');
    Route::post('accountcode/update/{id}', [MstAccountCodesController::class, 'update'])->name('accountcode.update');
    Route::post('accountcode/activate/{id}', [MstAccountCodesController::class, 'activate'])->name('accountcode.activate');
    Route::post('accountcode/deactivate/{id}', [MstAccountCodesController::class, 'deactivate'])->name('accountcode.deactivate');
    Route::post('accountcode/delete/{id}', [MstAccountCodesController::class, 'delete'])->name('accountcode.delete');
    Route::post('accountcode/deleteselected', [MstAccountCodesController::class, 'deleteselected'])->name('accountcode.deleteselected');
    Route::post('accountcode/deactiveselected', [MstAccountCodesController::class, 'deactiveselected'])->name('accountcode.deactiveselected');

    //TransDataKas
    Route::get('/transdatakas', [TransDataKasController::class, 'index'])->name('transdatakas.index');
    Route::post('/transdatakas', [TransDataKasController::class, 'index'])->name('transdatakas.index');
    Route::post('transdatakas/create', [TransDataKasController::class, 'store'])->name('transdatakas.store');
    Route::post('transdatakas/update/{id}', [TransDataKasController::class, 'update'])->name('transdatakas.update');
    Route::post('transdatakas/delete/{id}', [TransDataKasController::class, 'delete'])->name('transdatakas.delete');

    //TransDataBank
    Route::get('/transdatabank', [TransDataBankController::class, 'index'])->name('transdatabank.index');
    Route::post('/transdatabank', [TransDataBankController::class, 'index'])->name('transdatabank.index');
    Route::post('transdatabank/create', [TransDataBankController::class, 'store'])->name('transdatabank.store');
    Route::post('transdatabank/update/{id}', [TransDataBankController::class, 'update'])->name('transdatabank.update');
    Route::post('transdatabank/delete/{id}', [TransDataBankController::class, 'delete'])->name('transdatabank.delete');

    //SalesInvoice
    Route::get('/salesinvoice', [TransDataBankController::class, 'index'])->name('transdatabank.index');
    Route::post('/salesinvoice', [TransDataBankController::class, 'index'])->name('transdatabank.index');
    Route::post('salesinvoice/create', [TransDataBankController::class, 'store'])->name('transdatabank.store');
    Route::post('salesinvoice/update/{id}', [TransDataBankController::class, 'update'])->name('transdatabank.update');
    Route::post('salesinvoice/delete/{id}', [TransDataBankController::class, 'delete'])->name('transdatabank.delete');

    //TransSales
    Route::get('transsales', [TransSalesController::class, 'index'])->name('transsales.index');
    Route::post('transsales', [TransSalesController::class, 'index'])->name('transsales.index');
    Route::get('transsales/create', [TransSalesController::class, 'create'])->name('transsales.create');
    Route::get('transsales/getsalesorder/{id}', [TransSalesController::class, 'getsalesorder'])->name('transsales.getsalesorder');
    Route::post('transsales/store', [TransSalesController::class, 'store'])->name('transsales.store');
    Route::get('transsales/info/{id}', [TransSalesController::class, 'info'])->name('transsales.info');
    Route::get('transsales/edit/{id}', [TransSalesController::class, 'edit'])->name('transsales.edit');
    Route::post('transsales/update/{id}', [TransSalesController::class, 'update'])->name('transsales.update');
    Route::post('transsales/delete/{id}', [TransSalesController::class, 'delete'])->name('transsales.delete');
    Route::post('transsales/deleteselected', [TransSalesController::class, 'deleteselected'])->name('transsales.deleteselected');
    Route::post('transsales/deactiveselected', [TransSalesController::class, 'deactiveselected'])->name('transsales.deactiveselected');

    //TransPurchase
    Route::get('transpurchase', [TransPurchaseController::class, 'index'])->name('transpurchase.index');
    Route::post('transpurchase', [TransPurchaseController::class, 'index'])->name('transpurchase.index');
    Route::get('transpurchase/create', [TransPurchaseController::class, 'create'])->name('transpurchase.create');
    Route::get('transpurchase/getpurchaseorder/{id}', [TransPurchaseController::class, 'getpurchaseorder'])->name('transpurchase.getpurchaseorder');
    Route::post('transpurchase/store', [TransPurchaseController::class, 'store'])->name('transpurchase.store');
    Route::get('transpurchase/info/{id}', [TransPurchaseController::class, 'info'])->name('transpurchase.info');
    Route::get('transpurchase/edit/{id}', [TransPurchaseController::class, 'edit'])->name('transpurchase.edit');
    Route::post('transpurchase/update/{id}', [TransPurchaseController::class, 'update'])->name('transpurchase.update');
    Route::post('transpurchase/delete/{id}', [TransPurchaseController::class, 'delete'])->name('transpurchase.delete');
    Route::post('transpurchase/deleteselected', [TransPurchaseController::class, 'deleteselected'])->name('transpurchase.deleteselected');
    Route::post('transpurchase/deactiveselected', [TransPurchaseController::class, 'deactiveselected'])->name('transpurchase.deactiveselected');

    //TransImport
    Route::get('transimport', [TransImportController::class, 'index'])->name('transimport.index');
    Route::post('transimport', [TransImportController::class, 'index'])->name('transimport.index');
    Route::get('transimport/create', [TransImportController::class, 'create'])->name('transimport.create');
    Route::post('transimport/store', [TransImportController::class, 'store'])->name('transimport.store');
    Route::get('transimport/info/{id}', [TransImportController::class, 'info'])->name('transimport.info');
    Route::get('transimport/edit/{id}', [TransImportController::class, 'edit'])->name('transimport.edit');
    Route::post('transimport/update/{id}', [TransImportController::class, 'update'])->name('transimport.update');
    Route::post('transimport/delete/{id}', [TransImportController::class, 'delete'])->name('transimport.delete');
    Route::post('transimport/deleteselected', [TransImportController::class, 'deleteselected'])->name('transimport.deleteselected');
    Route::post('transimport/deactiveselected', [TransImportController::class, 'deactiveselected'])->name('transimport.deactiveselected');

    //GeneralLedger
    Route::get('generalledger', [GeneralLedgersController::class, 'index'])->name('generalledger.index');
    Route::post('generalledger', [GeneralLedgersController::class, 'index'])->name('generalledger.index');
    Route::get('generalledger/create', [GeneralLedgersController::class, 'create'])->name('generalledger.create');
    Route::post('generalledger/store', [GeneralLedgersController::class, 'store'])->name('generalledger.store');
});