<?php

use App\Http\Controllers\Location\AutoAuthController;
use App\Http\Controllers\CRMController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ScriptController;
use App\Http\Controllers\Admin\MapingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Location\CoborrowerController;
use App\Http\Controllers\Form\ImageController;
use App\Http\Controllers\Location\DealsController;
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
    Route::post('/setting/save', [SettingController::class, 'save'])->name('setting.save');

    Route::group(['as' => 'scripts.', 'prefix' => 'scripts'], function () {
        Route::get('/', [ScriptController::class, 'index'])->name('index');
        Route::get('/get/form', [ScriptController::class, 'getForm'])->name('get.form');
        Route::post('/store/{id?}', [ScriptController::class, 'store'])->name('store');
        Route::get('/delete/{id}', [ScriptController::class, 'delete'])->name('delete');
    });

    Route::group(['as' => 'mappings.', 'prefix' => 'mappings'], function () {
        Route::group(['as' => 'custom.', 'prefix' => 'extention'], function () {
            Route::get('/', [MapingController::class, 'customMaping'])->name('index');
            Route::get('/form/{id}', [MapingController::class, 'customMapingForm'])->name('form');
            Route::post('/form/submit', [MapingController::class, 'customMapingFormSubmit'])->name('form.submit');
            Route::get('/fields', [MapingController::class, 'customMapingFields'])->name('fields');
        });

        Route::group(['as' => 'ghl.', 'prefix' => 'ghl'], function () {
            Route::get('/form', [MapingController::class, 'ghlToNolocoForm'])->name('form');
            Route::post('/form/submit', [MapingController::class, 'ghlToNolocoFormSubmit'])->name('form.submit');
        });

        Route::group(['as' => 'customer.', 'prefix' => 'customer'], function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/form/{id?}', [CustomerController::class, 'form'])->name('form');
            Route::post('/form/submit', [CustomerController::class, 'formSubmit'])->name('form.submit');
            Route::get('/fields', [CustomerController::class, 'customMapingFields'])->name('fields');
        });
    });


});

Route::group(['as' => 'deals.', 'prefix' => 'deals'], function () {
    Route::get('/management', [DealsController::class, 'index'])->name('setting');
    Route::get('/inventories/search', [DealsController::class, 'searchInventory'])->name('inventories.search');
    Route::get('/get/customers', [DealsController::class, 'getCustomers'])->name('get.customers');
    Route::get('/get/deals', [DealsController::class, 'getDeals'])->name('get.deals');
    Route::get('/create/setting', [DealsController::class, 'create'])->name('create.setting');
});

Route::group(['as' => 'coborrower.', 'prefix' => 'coborrower'], function () {
    Route::get('/management', [CoborrowerController::class, 'index'])->name('setting');
    Route::get('/contacts/search', [CoborrowerController::class, 'contactsSearch'])->name('contacts.search');
    Route::get('/get/customer', [CoborrowerController::class, 'getCustomer'])->name('get.customer');
    Route::get('/set/deal', [CoborrowerController::class, 'setDeal'])->name('set.deal');
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

    // dispatch((new CheckCustomValuesJob('HuVkfWx59Pv4mUMgGRTp', 9)));
    // return response()->json(['success' => 'We are matching custom values']);

    dispatch((new ProcessRefreshToken()));
});



require __DIR__ . '/auth.php';
