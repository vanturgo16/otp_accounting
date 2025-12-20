<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransCashBookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneralLedgersController;
use App\Http\Controllers\MstAccountCodesController;
use App\Http\Controllers\MstAccountTypesController;
use App\Http\Controllers\MstBankAccountController;
use App\Http\Controllers\MstPpnController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransSalesController;
use App\Http\Controllers\TransPurchaseController;

use App\Http\Controllers\EntityListController;
use App\Http\Controllers\TransDataBankController;
use App\Http\Controllers\TransDataKasController;
use App\Http\Controllers\TransImportController;

//Route Login
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('auth/login', [AuthController::class, 'postlogin'])->name('postlogin')->middleware("throttle:5,2");
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth','clear.permission.cache','permission:Akunting_dashboard'])->group(function () {
    //Dashboard
    Route::controller(DashboardController::class)->group(function () {
        Route::prefix('dashboard')->group(function () {
            Route::get('/', 'index')->name('dashboard');
            Route::get('/get-data-summary', 'getDataSummary')->name('getDataSummary');
            Route::post('/', 'switchTheme')->name('switchTheme');
        });
    });

    //Master PPN
    Route::controller(MstPpnController::class)->group(function () {
        Route::prefix('ppn')->middleware('permission:Akunting_master_data')->group(function () {
            Route::get('/', 'index')->name('ppn.index');
            Route::post('/update/{id}', 'update')->name('ppn.update');
            Route::prefix('modal')->group(function () {
                Route::get('/edit/{id}', 'modalEdit')->name('ppn.modal.edit');
            });
        });
    });

    //Master Bank Account
    Route::controller(MstBankAccountController::class)->group(function () {
        Route::prefix('bankaccount')->middleware('permission:Akunting_master_data')->group(function () {
            Route::get('/', 'index')->name('bankaccount.index');
            Route::post('/store', 'store')->name('bankaccount.store');
            Route::post('/update/{id}', 'update')->name('bankaccount.update');
            Route::post('/activate/{id}', 'activate')->name('bankaccount.activate');
            Route::post('/deactivate/{id}', 'deactivate')->name('bankaccount.deactivate');
            Route::prefix('modal')->group(function () {
                Route::get('/new', 'modalAdd')->name('bankaccount.modal.new');
                Route::get('/info/{id}', 'modalInfo')->name('bankaccount.modal.info');
                Route::get('/edit/{id}', 'modalEdit')->name('bankaccount.modal.edit');
                Route::get('/activate/{id}', 'modalActivate')->name('bankaccount.modal.activate');
                Route::get('/deactivate/{id}', 'modalDeactivate')->name('bankaccount.modal.deactivate');
            });
        });
    });

    //AccountType
    Route::controller(MstAccountTypesController::class)->group(function () {
        Route::prefix('accounttype')->middleware('permission:Akunting_master_data')->group(function () {
            Route::get('/', 'index')->name('accounttype.index');
            Route::post('/store', 'store')->name('accounttype.store');
            Route::post('/update/{id}', 'update')->name('accounttype.update');
            Route::post('/activate/{id}', 'activate')->name('accounttype.activate');
            Route::post('/deactivate/{id}', 'deactivate')->name('accounttype.deactivate');
            Route::post('/delete/{id}', 'delete')->name('accounttype.delete');

            Route::prefix('modal')->group(function () {
                Route::get('/new', 'modalAdd')->name('accounttype.modal.new');
                Route::get('/info/{id}', 'modalInfo')->name('accounttype.modal.info');
                Route::get('/edit/{id}', 'modalEdit')->name('accounttype.modal.edit');
                Route::get('/activate/{id}', 'modalActivate')->name('accounttype.modal.activate');
                Route::get('/deactivate/{id}', 'modalDeactivate')->name('accounttype.modal.deactivate');
                Route::get('/delete/{id}', 'modalDelete')->name('accounttype.modal.delete');
            });
        });
    });
    
    //AccountCode
    Route::controller(MstAccountCodesController::class)->group(function () {
        Route::prefix('accountcode')->middleware('permission:Akunting_master_data')->group(function () {
            Route::get('/', 'index')->name('accountcode.index');
            Route::post('/store', 'store')->name('accountcode.store');
            Route::get('/edit/{id}', 'edit')->name('accountcode.edit');
            Route::post('/update/{id}', 'update')->name('accountcode.update');
            Route::post('/activate/{id}', 'activate')->name('accountcode.activate');
            Route::post('/deactivate/{id}', 'deactivate')->name('accountcode.deactivate');
            Route::post('/delete/{id}', 'delete')->name('accountcode.delete');

            Route::prefix('modal')->group(function () {
                Route::get('/new', 'modalAdd')->name('accountcode.modal.new');
                Route::get('/info/{id}', 'modalInfo')->name('accountcode.modal.info');
                Route::get('/edit/{id}', 'modalEdit')->name('accountcode.modal.edit');
                Route::get('/activate/{id}', 'modalActivate')->name('accountcode.modal.activate');
                Route::get('/deactivate/{id}', 'modalDeactivate')->name('accountcode.modal.deactivate');
                Route::get('/delete/{id}', 'modalDelete')->name('accountcode.modal.delete');
            });
        });
    });

    //TransSales
    Route::controller(TransSalesController::class)->group(function () {
        Route::prefix('transsales')->middleware('permission:Akunting_sales')->group(function () {
            Route::get('/getdeliverynote/{id}', 'getDeliveryNote')->name('transsales.getdeliverynote');
            Route::get('/getsoprice-from-dn', 'getSOPriceFromDN')->name('transsales.getSOPriceFromDN');
            Route::get('/gettotalprice/{id}/{ppnRate}/{type}', 'getTotalPrice')->name('transsales.gettotalprice');
            Route::get('/getcustomer-from-dn/{id}', 'getCustomerFromDN')->name('transsales.getCustomerFromDN');
            // Local
            Route::prefix('local')->group(function () {
                // Modal
                Route::prefix('modal')->group(function () {
                    Route::get('/total-transaction/{id}', 'modalTransactionLocal')->name('transsales.local.modal.listTT');
                    Route::get('/info/{id}', 'modalInfoLocal')->name('transsales.local.modal.info');
                    Route::get('/delete/{id}', 'modalDeleteLocal')->middleware('permission:Akunting_master_data')->name('transsales.local.modal.delete');
                });
                Route::get('/', 'indexLocal')->name('transsales.local.index');
                Route::get('create', 'createLocal')->name('transsales.local.create');
                Route::post('store', 'storeLocal')->name('transsales.local.store');
                Route::get('info/{id}', 'infoLocal')->name('transsales.local.info');
                Route::middleware('permission:Akunting_master_data')->group(function () {
                    Route::get('edit/{id}', 'editLocal')->name('transsales.local.edit');
                    Route::post('update/{id}', 'updateLocal')->name('transsales.local.update');
                    Route::post('delete/{id}', 'deleteLocal')->name('transsales.local.delete');
                });
                Route::get('print/{id}', 'printLocal')->name('transsales.local.print');
            });
            // Export
            Route::prefix('export')->group(function () {
                // Modal
                Route::prefix('modal')->group(function () {
                    Route::get('/total-transaction/{id}', 'modalTransactionExport')->name('transsales.export.modal.listTT');
                    Route::get('/info/{id}', 'modalInfoExport')->name('transsales.export.modal.info');
                    Route::get('/delete/{id}', 'modalDeleteExport')->middleware('permission:Akunting_master_data')->name('transsales.export.modal.delete');
                });
                Route::get('/', 'indexExport')->name('transsales.export.index');
                Route::get('create', 'createExport')->name('transsales.export.create');
                Route::post('store', 'storeExport')->name('transsales.export.store');
                Route::get('info/{id}', 'infoExport')->name('transsales.export.info');
                Route::middleware('permission:Akunting_master_data')->group(function () {
                    Route::get('edit/{id}', 'editExport')->name('transsales.export.edit');
                    Route::post('update/{id}', 'updateExport')->name('transsales.export.update');
                    Route::post('delete/{id}', 'deleteExport')->name('transsales.export.delete');
                });
                Route::get('print/{id}', 'printExport')->name('transsales.export.print');
            });
        });
    });

    //TransPurchase
    Route::controller(TransPurchaseController::class)->group(function () {
        Route::prefix('transpurchase')->middleware('permission:Akunting_purchase')->group(function () {
            Route::get('/getprice-from-grn', 'getPriceFromGRN')->name('transpurchase.getPriceFromGRN');
            Route::get('/getdetail-grn/{id}', 'getDetailGRN')->name('transpurchase.getDetailGRN');
            Route::get('/getdetail-trans/{id}', 'getDetail')->name('transpurchase.getDetail');
            
            // Modal
            Route::prefix('modal')->group(function () {
                Route::get('/total-transaction/{id}', 'modalTransaction')->name('transpurchase.modal.listTT');
                Route::get('/info/{id}', 'modalInfo')->name('transpurchase.modal.info');
                Route::get('/delete/{id}', 'modalDelete')->middleware('permission:Akunting_master_data')->name('transpurchase.modal.delete');
            });

            Route::get('/', 'index')->name('transpurchase.index');
            Route::get('/create', 'create')->name('transpurchase.create');
            Route::post('/store', 'store')->name('transpurchase.store');
            Route::middleware('permission:Akunting_master_data')->group(function () {
                Route::get('edit/{id}', 'edit')->name('transpurchase.edit');
                Route::post('update/{id}', 'update')->name('transpurchase.update');
                Route::post('delete/{id}', 'delete')->name('transpurchase.delete');
            });
        });
    });

    //Cash Book
    Route::controller(TransCashBookController::class)->group(function () {
        Route::prefix('cashbook')->middleware('permission:Akunting_generalledger')->group(function () {
            // Modal
            Route::prefix('modal')->group(function () {
                Route::get('/info/{id}', 'modalInfo')->name('cashbook.modal.info');
                Route::get('/delete/{id}', 'modalDelete')->middleware('permission:Akunting_master_data')->name('cashbook.modal.delete');
            });
            Route::get('/', 'index')->name('cashbook.index');
            Route::get('/create', 'create')->name('cashbook.create');
            Route::post('/store', 'store')->name('cashbook.store');
            Route::middleware('permission:Akunting_master_data')->group(function () {
                Route::get('edit/{id}', 'edit')->name('cashbook.edit');
                Route::post('update/{id}', 'update')->name('cashbook.update');
                Route::post('delete/{id}', 'delete')->name('cashbook.delete');
            });
            Route::get('print/{id}', 'print')->name('cashbook.print');
        });
    });

    //GeneralLedger
    Route::controller(GeneralLedgersController::class)->group(function () {
        Route::prefix('general-ledger')->middleware('permission:Akunting_generalledger')->group(function () {
            Route::get('/', 'index')->name('generalledger.index');
            Route::prefix('modal')->group(function () {
                Route::get('/info/{source}/{id}', 'modalInfo')->name('generalledger.modal.info');
            });
        });
    });

    //Report
    Route::controller(ReportController::class)->group(function () {
        Route::prefix('report')->middleware('permission:Akunting_master_data')->group(function () {
            Route::get('/', 'index')->name('report.monthly.index');
        });
    });

    // //TransDataKas
    // Route::get('/transdatakas', [TransDataKasController::class, 'index'])->name('transdatakas.index');
    // Route::post('/transdatakas', [TransDataKasController::class, 'index'])->name('transdatakas.index');
    // Route::post('transdatakas/create', [TransDataKasController::class, 'store'])->name('transdatakas.store');
    // Route::post('transdatakas/update/{id}', [TransDataKasController::class, 'update'])->name('transdatakas.update');
    // Route::post('transdatakas/delete/{id}', [TransDataKasController::class, 'delete'])->name('transdatakas.delete');
    // //TransDataBank
    // Route::get('/transdatabank', [TransDataBankController::class, 'index'])->name('transdatabank.index');
    // Route::post('/transdatabank', [TransDataBankController::class, 'index'])->name('transdatabank.index');
    // Route::post('transdatabank/create', [TransDataBankController::class, 'store'])->name('transdatabank.store');
    // Route::post('transdatabank/update/{id}', [TransDataBankController::class, 'update'])->name('transdatabank.update');
    // Route::post('transdatabank/delete/{id}', [TransDataBankController::class, 'delete'])->name('transdatabank.delete');
    // //SalesInvoice
    // Route::get('/salesinvoice', [TransDataBankController::class, 'index'])->name('transdatabank.index');
    // Route::post('/salesinvoice', [TransDataBankController::class, 'index'])->name('transdatabank.index');
    // Route::post('salesinvoice/create', [TransDataBankController::class, 'store'])->name('transdatabank.store');
    // Route::post('salesinvoice/update/{id}', [TransDataBankController::class, 'update'])->name('transdatabank.update');
    // Route::post('salesinvoice/delete/{id}', [TransDataBankController::class, 'delete'])->name('transdatabank.delete');

    // //TransImport
    // Route::controller(TransImportController::class)->group(function () {
    //     Route::prefix('transimport')->middleware('permission:Akunting_import')->group(function () {
    //         Route::get('/', 'index')->name('transimport.index');
    //         Route::post('/', 'index')->name('transimport.index');
    //         Route::get('/create', 'create')->name('transimport.create');
    //         Route::post('/store', 'store')->name('transimport.store');
    //         Route::get('/info/{id}', 'info')->name('transimport.info');
    //     });
    // });

    // //ENTITY LIST
    // Route::controller(EntityListController::class)->group(function () {
    //     Route::prefix('entitylist')->middleware('permission:Akunting_master_data')->group(function () {
    //         Route::get('/neraca', 'neraca')->name('entitylist.neraca');
    //         Route::get('/hpp', 'hpp')->name('entitylist.hpp');
    //         Route::get('/labarugi', 'labarugi')->name('entitylist.labarugi');
    //     });
    // });

    // //REPORT
    // Route::controller(ReportController::class)->group(function () {
    //     Route::prefix('report')->middleware('permission:Akunting_report')->group(function () {
    //         //Neraca
    //         Route::get('/neraca', 'neraca')->name('report.neraca');
    //         Route::get('/neraca/detail/{id}', 'neracaDetail')->name('report.neraca.detail');
    //         Route::get('/neraca/view', 'neracaView')->name('report.neraca.view');
    //         Route::post('/neraca/generate', 'neracaGenerate')->name('report.neraca.generate');
    //         //Hpp
    //         Route::get('/hpp', 'hpp')->name('report.hpp');
    //         Route::get('/hpp/detail/{id}', 'hppDetail')->name('report.hpp.detail');
    //         Route::get('/hpp/view', 'hppView')->name('report.hpp.view');
    //         Route::post('/hpp/generate', 'hppGenerate')->name('report.hpp.generate');
    //         //LabaRugi
    //         Route::get('/labarugi', 'labarugi')->name('report.labarugi');
    //     });
    // });

});

