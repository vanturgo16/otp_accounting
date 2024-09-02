<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneralLedgersController;
use App\Http\Controllers\EntityListController;
use App\Http\Controllers\MstAccountCodesController;
use App\Http\Controllers\MstAccountTypesController;
use App\Http\Controllers\MstBankAccountController;
use App\Http\Controllers\MstPpnController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransDataBankController;
use App\Http\Controllers\TransDataKasController;
use App\Http\Controllers\TransSalesController;
use App\Http\Controllers\TransPurchaseController;
use App\Http\Controllers\TransImportController;

//Route Login
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('auth/login', [AuthController::class, 'postlogin'])->name('postlogin')->middleware("throttle:5,2");
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



Route::middleware(['auth','clear.permission.cache','permission:Akunting_dashboard'])->group(function () {
    //Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        //Master PPN
        Route::controller(MstPpnController::class)->group(function () {
            Route::prefix('ppn')->middleware('permission:Akunting_master_data')->group(function () {
                Route::get('/', 'index')->name('ppn.index');
                Route::post('/', 'index')->name('ppn.index');
                Route::post('/store', 'store')->name('ppn.store');
            });
        });

        //Master Bank Account
        Route::controller(MstBankAccountController::class)->group(function () {
            Route::prefix('bankaccount')->middleware('permission:Akunting_master_data')->group(function () {
                Route::get('/', 'index')->name('bankaccount.index');
                Route::post('/', 'index')->name('bankaccount.index');
                Route::post('/store', 'store')->name('bankaccount.store');
            });
        });

        //AccountType
        Route::controller(MstAccountTypesController::class)->group(function () {
            Route::prefix('accounttype')->middleware('permission:Akunting_master_data')->group(function () {
                Route::get('/', 'index')->name('accounttype.index');
                Route::post('/', 'index')->name('accounttype.index');
                Route::post('/store', 'store')->name('accounttype.store');
                Route::get('/edit/{id}', 'edit')->name('accounttype.edit');
                Route::post('/update/{id}', 'update')->name('accounttype.update');
                Route::post('/activate/{id}', 'activate')->name('accounttype.activate');
                Route::post('/deactivate/{id}', 'deactivate')->name('accounttype.deactivate');
                Route::post('/delete/{id}', 'delete')->name('accounttype.delete');
                Route::post('/deleteselected', 'deleteselected')->name('accounttype.deleteselected');
                Route::post('/deactiveselected', 'deactiveselected')->name('accounttype.deactiveselected');
            });
        });
        
        //AccountCode
        Route::controller(MstAccountCodesController::class)->group(function () {
            Route::prefix('accountcode')->middleware('permission:Akunting_master_data')->group(function () {
                Route::get('/', 'index')->name('accountcode.index');
                Route::post('/', 'index')->name('accountcode.index');
                Route::post('/store', 'store')->name('accountcode.store');
                Route::get('/edit/{id}', 'edit')->name('accountcode.edit');
                Route::post('/update/{id}', 'update')->name('accountcode.update');
                Route::post('/activate/{id}', 'activate')->name('accountcode.activate');
                Route::post('/deactivate/{id}', 'deactivate')->name('accountcode.deactivate');
                Route::post('/delete/{id}', 'delete')->name('accountcode.delete');
                Route::post('/deleteselected', 'deleteselected')->name('accountcode.deleteselected');
                Route::post('/deactiveselected', 'deactiveselected')->name('accountcode.deactiveselected');
            });
        });

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
        Route::controller(TransSalesController::class)->group(function () {
            Route::prefix('transsales')->middleware('permission:Akunting_sales')->group(function () {
                Route::get('/getdeliverynote/{id}', 'getDeliveryNote')->name('transsales.getdeliverynote');
                Route::get('/getsalesorder', 'getSalesOrder')->name('transsales.getsalesorder');
                Route::get('/gettotalprice/{id}', 'getTotalPrice')->name('transsales.gettotalprice');

                // Local
                Route::get('local', 'indexLocal')->name('transsales.local.index');
                Route::post('local', 'indexLocal')->name('transsales.local.index');
                Route::get('local/create', 'createLocal')->name('transsales.local.create');
                Route::post('local/store', 'storeLocal')->name('transsales.local.store');
                Route::get('local/info/{id}', 'infoLocal')->name('transsales.local.info');
                Route::get('local/print/{id}', 'printLocal')->name('transsales.local.print');

                // Export
                Route::get('export', 'indexExport')->name('transsales.export.index');
                Route::post('export', 'indexExport')->name('transsales.export.index');
                Route::get('export/create', 'createExport')->name('transsales.export.create');
                Route::post('export/store', 'storeExport')->name('transsales.export.store');
                Route::get('export/info/{id}', 'infoExport')->name('transsales.export.info');
                Route::get('export/print/{id}', 'printExport')->name('transsales.export.print');
            });
        });

        //TransPurchase
        Route::controller(TransPurchaseController::class)->group(function () {
            Route::prefix('transpurchase')->middleware('permission:Akunting_purchase')->group(function () {
                Route::get('/getpurchaseorder/{id}', 'getpurchaseorder')->name('transpurchase.getpurchaseorder');
                Route::get('/getgoodreceiptnote/{id}', 'getgoodReceiptNote')->name('transpurchase.getgoodReceiptNote');

                Route::get('/', 'index')->name('transpurchase.index');
                Route::post('/', 'index')->name('transpurchase.index');
                Route::get('/create', 'create')->name('transpurchase.create');
                Route::post('/store', 'store')->name('transpurchase.store');
                Route::get('/info/{id}', 'info')->name('transpurchase.info');
            });
        });

        //TransImport
        Route::controller(TransImportController::class)->group(function () {
            Route::prefix('transimport')->middleware('permission:Akunting_import')->group(function () {
                Route::get('/', 'index')->name('transimport.index');
                Route::post('/', 'index')->name('transimport.index');
                Route::get('/create', 'create')->name('transimport.create');
                Route::post('/store', 'store')->name('transimport.store');
                Route::get('/info/{id}', 'info')->name('transimport.info');
            });
        });

        //GeneralLedger
        Route::controller(GeneralLedgersController::class)->group(function () {
            Route::prefix('generalledger')->middleware('permission:Akunting_generalledger')->group(function () {
                Route::get('/', 'index')->name('generalledger.index');
                Route::post('/', 'index')->name('generalledger.index');
                Route::get('/create', 'create')->name('generalledger.create');
                Route::post('/store', 'store')->name('generalledger.store');
            });
        });

        //ENTITY LIST
        Route::controller(EntityListController::class)->group(function () {
            Route::prefix('entitylist')->middleware('permission:Akunting_master_data')->group(function () {
                Route::get('/neraca', 'neraca')->name('entitylist.neraca');
                Route::get('/hpp', 'hpp')->name('entitylist.hpp');
                Route::get('/labarugi', 'labarugi')->name('entitylist.labarugi');
            });
        });

        //REPORT
        Route::controller(ReportController::class)->group(function () {
            Route::prefix('report')->middleware('permission:Akunting_report')->group(function () {
                //Neraca
                Route::get('/neraca', 'neraca')->name('report.neraca');
                Route::get('/neraca/detail/{id}', 'neracaDetail')->name('report.neraca.detail');
                Route::get('/neraca/view', 'neracaView')->name('report.neraca.view');
                Route::post('/neraca/generate', 'neracaGenerate')->name('report.neraca.generate');
                //Hpp
                Route::get('/hpp', 'hpp')->name('report.hpp');
                Route::get('/hpp/detail/{id}', 'hppDetail')->name('report.hpp.detail');
                Route::get('/hpp/view', 'hppView')->name('report.hpp.view');
                Route::post('/hpp/generate', 'hppGenerate')->name('report.hpp.generate');
                //LabaRugi
                Route::get('/labarugi', 'labarugi')->name('report.labarugi');
            });
        });

    });

