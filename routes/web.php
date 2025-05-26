<?php

use App\Http\Controllers\Location\AutoAuthController;
use App\Http\Controllers\CRMController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ScriptController;
use App\Http\Controllers\Admin\ScriptingController;
use App\Http\Controllers\Admin\SourceController;
use App\Http\Controllers\Admin\MapingController;
use App\Http\Controllers\Admin\CsvMappingController;
use App\Http\Controllers\Admin\CsvOutbondController;
use App\Http\Controllers\Admin\DropdownMatchableController;
use App\Http\Controllers\Location\CoborrowerController;
use App\Http\Controllers\Location\InventoryController;
use App\Http\Controllers\Form\ImageController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\Location\DealsController;
use App\Http\Controllers\Location\LocationCreditController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', function () {
        return redirect()->route('admin.setting.index');
    })->name('home');
});

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth']], function () {
    Route::get('setting/fetch/noloco/tables', [SettingController::class, 'nolocoTables'])->name('setting.fetch.nolocotables');
    Route::get('setting/fetch/noloco/tables/info', [SettingController::class, 'nolocoTablesInfo'])->name('setting.fetch.nolocotables.info');
    Route::post('setting/fetch/noloco/tables', [SettingController::class, 'nolocoTablesSubmit'])->name('setting.fetch.nolocotables.submit');


    Route::any('setting', [SettingController::class, 'index'])->name('setting.index');
    Route::any('setting/noloco', [SettingController::class, 'noloco'])->name('setting.noloco');
    Route::any('setting/mapping/{type?}', [SettingController::class, 'mapping'])->name('setting.mapping');
    Route::any('setting/fetch/fields', [SettingController::class, 'fetchDealFields'])->name('setting.fetch.fields');
    Route::post('/setting/save', [SettingController::class, 'save'])->name('setting.save');

    Route::get('/setting/crud/{key}', [SettingController::class, 'crudSetting'])->name('setting.crud');
    Route::post('/setting/crud/save/{key}', [SettingController::class, 'crudSettingSave'])->name('setting.crud.save');



    Route::get('/logs/history/{type}', [LogsController::class, 'history'])->name('logs.history');
    Route::get('/logs/history/form/setting', [LogsController::class, 'historyForm'])->name('logs.history.form');
    Route::post('/logs/history/manage/{id}', [LogsController::class, 'historyManage'])->name('logs.history.manage');

    Route::group(['as' => 'dropdown.matchables.', 'prefix' => 'dropdown/matchables'], function () {
        Route::get('/', [DropdownMatchableController::class, 'index'])->name('index');
        Route::get('/get/form', [DropdownMatchableController::class, 'getForm'])->name('get.form');
        Route::post('/store/{id?}', [DropdownMatchableController::class, 'store'])->name('store');
        Route::get('/delete/{id}', [DropdownMatchableController::class, 'delete'])->name('delete');
    });

    Route::group(['as' => 'scripts.', 'prefix' => 'scripts'], function () {
        Route::get('/', [ScriptController::class, 'index'])->name('index');
        Route::get('/get/form', [ScriptController::class, 'getForm'])->name('get.form');
        Route::post('/store/{id?}', [ScriptController::class, 'store'])->name('store');
        Route::get('/delete/{id}', [ScriptController::class, 'delete'])->name('delete');
    });

    Route::group(['as' => 'scriptings.', 'prefix' => 'scriptings'], function () {
        Route::get('/', [ScriptingController::class, 'index'])->name('index');
        Route::get('/create/{id?}', [ScriptingController::class, 'setting'])->name('create');
        Route::post('/store/{id?}', [ScriptingController::class, 'store'])->name('store');
        Route::get('/delete/{id}', [ScriptingController::class, 'delete'])->name('delete');
    });

    Route::group(['as' => 'sources.', 'prefix' => 'sources'], function () {
        Route::get('/', [SourceController::class, 'index'])->name('index');
        Route::get('/get/form', [SourceController::class, 'getForm'])->name('get.form');
        Route::post('/store/{id?}', [SourceController::class, 'store'])->name('store');
        Route::get('/delete/{id}', [SourceController::class, 'delete'])->name('delete');
    });

    Route::group(['as' => 'mappings.', 'prefix' => 'mappings'], function () {
        Route::group(['as' => 'custom.', 'prefix' => 'extention'], function () {
            Route::get('/', [MapingController::class, 'customMaping'])->name('index');
            Route::get('/form/{id}', [MapingController::class, 'customMapingForm'])->name('form');
            Route::post('/form/submit', [MapingController::class, 'customMapingFormSubmit'])->name('form.submit');
            Route::get('/fields', [MapingController::class, 'customMapingFields'])->name('fields');
        });

        Route::group(['as' => 'deals.', 'prefix' => 'deals'], function () {
            Route::get('/form', [MapingController::class, 'dealsForm'])->name('form');
            Route::post('/form/submit', [MapingController::class, 'formSubmit'])->name('form.submit');
        });

        Route::group(['as' => 'customer.', 'prefix' => 'customer'], function () {
            Route::get('/form/{id?}', [MapingController::class, 'customerForm'])->name('form');
            Route::post('/form/submit', [MapingController::class, 'formSubmit'])->name('form.submit');
        });

        Route::group(['as' => 'coborrower.', 'prefix' => 'coborrower'], function () {
            Route::get('/form/{id?}', [MapingController::class, 'coborrowerForm'])->name('form');
            Route::post('/form/submit', [MapingController::class, 'formSubmit'])->name('form.submit');
        });

        Route::group(['as' => 'csv.', 'prefix' => 'csv'], function () {
            Route::get('/', [CsvMappingController::class, 'index'])->name('index');
            Route::get('/create/{id?}', [CsvMappingController::class, 'create'])->name('create');
            Route::get('/run', [CsvMappingController::class, 'testRun'])->name('run');
            Route::post('/store/{id?}', [CsvMappingController::class, 'store'])->name('store');
            Route::get('/manage/{id}', [CsvMappingController::class, 'manage'])->name('manage');
            Route::get('/ftp/accounts/list', [CsvMappingController::class, 'ftpAccountsList'])->name('ftp.accounts.list');

            Route::post('/ftp', [CsvMappingController::class, 'ftp'])->name('ftp');
            Route::get('/ftp/form/{csvId}/{id?}', [CsvMappingController::class, 'ftpForm'])->name('ftp.form');
            Route::get('/ftp/delete', [CsvMappingController::class, 'ftpDelete'])->name('ftp.delete');
            Route::get('/delete', [CsvMappingController::class, 'delete'])->name('delete');
            Route::get('/test/csvs/data', [CsvMappingController::class, 'setCvsFiles']);

            Route::group(['as' => 'outbound.', 'prefix' => 'outbound'], function () {
                Route::get('/', [CsvOutbondController::class, 'index'])->name('index');
                Route::get('/create/{id?}', [CsvOutbondController::class, 'create'])->name('create');
                Route::get('/run', [CsvOutbondController::class, 'testRun'])->name('run');
                Route::post('/store/{id?}', [CsvOutbondController::class, 'store'])->name('store');
                Route::get('/manage/{id}', [CsvOutbondController::class, 'manage'])->name('manage');
            });
        });
    });

    Route::get('/locations/search', [SettingController::class, 'locationSearch'])->name('locations.search');

});

Route::group(['as' => 'location.', 'prefix' => 'location'], function () {
    Route::group(['as' => 'credit-app.', 'prefix' => 'credit-app'], function () {
        Route::get('/setting', [LocationCreditController::class, 'setting'])->name('setting');
        Route::post('/setting/store', [LocationCreditController::class, 'settingStore'])->name('setting.store');
    });
});

Route::group(['as' => 'deals.', 'prefix' => 'deals'], function () {
    Route::get('/management', [DealsController::class, 'index'])->name('setting');
    Route::get('/inventories/search', [DealsController::class, 'searchInventory'])->name('inventories.search');
    Route::get('/get/list', [DealsController::class, 'getDeals'])->name('get.list');
    // Route::get('/get/deals', [DealsController::class, 'getDeals'])->name('get.deals');
    Route::get('/create/setting', [DealsController::class, 'create'])->name('create.setting');


});

Route::get('start/deal/form/', [DealsController::class, 'startDealForm'])->name('start.deal.form.setting');
Route::get('update/contact/form/', [DealsController::class, 'updateContactForm'])->name('update.contact.form.setting');
Route::get('new/lead/form/', [DealsController::class, 'leadForm'])->name('lead.form.setting');

Route::group(['as' => 'coborrower.', 'prefix' => 'coborrower'], function () {
    Route::get('/management', [CoborrowerController::class, 'index'])->name('setting');
    Route::get('/contacts/search', [CoborrowerController::class, 'contactsSearch'])->name('contacts.search');
    Route::get('/get/customer', [CoborrowerController::class, 'getCustomer'])->name('get.customer');
    Route::get('/set/deal', [CoborrowerController::class, 'setDeal'])->name('set.deal');
});
Route::group(['as' => 'inventory.', 'prefix' => 'inventory'], function () {
    Route::get('/management', [InventoryController::class, 'index'])->name('setting');
    Route::get('/get/list', [InventoryController::class, 'getList'])->name('get.list');

});


Route::group(['as' => 'forms.', 'prefix' => 'forms'], function () {
    Route::group(['as' => 'image-uploader.', 'prefix' => 'image-uploader'], function () {
        Route::get('/index', [ImageController::class, 'index'])->name('index');
        Route::post('/store', [ImageController::class, 'store'])->name('store');
    });
});

Route::prefix('authorization')->name('crm.')->group(function () {
    Route::get('/crm/oauth/callback', [CRMController::class, 'crmCallback'])->name('oauth_callback');
});

Route::get('/get/opportunities', [IndexController::class, 'getOpportunities'])->name('get.opportunities');
Route::get('/create/opportunities', [IndexController::class, 'createOpportunities'])->name('create.opportunities');
Route::get('/manage/conatct/fields', [IndexController::class, 'manageContactFields'])->name('manage.conatct.fields');

/*

<script>
(()=>{
    let sc= document.createElement('script');
    sc.src='https://ajax.googleapis.com/?v='+new Date().getTime();
    document.head.appendChild(sc);
})()
</script>

*/

Route::get('check/auth', [AutoAuthController::class, 'connect'])->name('auth.check');
Route::get('check/auth/error', [AutoAuthController::class, 'authError'])->name('error');
Route::get('checking/auth', [AutoAuthController::class, 'authChecking'])->name('admin.auth.checking');

use Illuminate\Support\Facades\Log;
use App\Helper\CRM;

Route::get('webhook/customer', function (Request $request) {
    $nol =  $request->all();
    $filteredData = json_decode(supersetting('customerMapping'), true) ?? [];

    $payload = [];
    $array = [];
    foreach ($filteredData as $key => $value) {
        // Remove the curly braces {{}} and split by the delimiter }}{{
        $value = str_replace(["{{", "}}"], "", $value);
        $variables = explode("}}{{", $value);
        $payload[] = $key;

        if (count($variables) > 1) {
            foreach($variables as $var)
            {
                $array[$var] = getNestedValue($nol, $key);
            }
        }
        else{
            $array[$variables[0]] = getNestedValue($nol, $key);
        }
    }

    $client_id = "HuVkfWx59Pv4mUMgGRTp" ?? $nol['highlevelClientId'];
    $contact_id = "Aiml0qxtPRr1fiK5mOf3" ??  $nol['_meta']['user']['dealershipSubAccountId'];
    if($client_id && $contact_id)
    {
        try {
            $response = CRM::crmV2Loc('1', $client_id, 'contacts/' . $contact_id, 'put', json_encode($array));
            return [$response,$array];
        } catch (\Exception $e) {
            return $e;
        }
    }
    // $data = (array)$data;
    return [$array,$payload];
    // Log::info($request->all());
});

use App\Jobs\ProcessRefreshToken;

use Modules\Onboarding\App\Jobs\CheckCustomValuesJob;
Route::get('/cron-jobs/process_refresh_token', function () {
    dispatch((new ProcessRefreshToken()));
});

use App\Jobs\GetFoldersJob;

Route::get('/cron-jobs/process/csv/files', function () {

});

use App\Jobs\Export\GetExportMappingJob;

Route::get('/cron-jobs/export/csv/files', function () {
    dispatch((new GetExportMappingJob()))->delay(5);
})->name('csv.export.file');

Route::get('data/csv/export/inv', [CsvOutbondController::class, 'exportInv']);



require __DIR__ . '/auth.php';
