<?php
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\BanniereController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\LivraisonController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\PaymentMethodeController;
use App\Http\Controllers\Api\ProduitController;
use App\Http\Controllers\Api\PromoBanniereController;
use App\Http\Controllers\Api\PromoProductController;
use App\Http\Controllers\Api\RegleController;
use App\Http\Controllers\Api\ReviewsController;
use App\Http\Controllers\Api\SocialinkController;
use App\Http\Controllers\Api\SubCategorieController;
use App\Http\Middleware\CheckRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PackController;
use App\Http\Controllers\Api\ProductImageController;

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
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']); 
    Route::put('/updateUserProfile', [AuthController::class, 'updateUserProfile']);   
 
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'admin'
], function ($router) {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/register', [AdminAuthController::class, 'register'])->middleware(CheckRole::class);
    Route::post('/logout', [AdminAuthController::class, 'logout']);
    Route::post('/refresh', [AdminAuthController::class, 'refresh']);
    Route::get('/user-profile', [AdminAuthController::class, 'userProfile']);   
    Route::put('/updateUserProfile', [AdminAuthController::class, 'updateUserProfile']);   
 
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'addresse'
], function ($router) {
    Route::middleware('auth')->group(function () {
        Route::post('/store', [AddressController::class, 'store']);
        Route::get('/getUserAddress', [AddressController::class, 'getUserAddress']);
        Route::put('/update/{id}', [AddressController::class, 'update']);
    });
});
Route::group(['prefix' => 'category'], function () {
    Route::controller(CategorieController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('show/{id}', 'show');
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });
});
Route::group(['prefix' => 'subcategory'], function () {
    Route::controller(SubCategorieController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('show/{id}', 'show');
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });
});
Route::group(['prefix' => 'product'], function () {
    Route::controller(ProduitController::class)->group(function () {
        Route::get('/', 'searchProducts');
        Route::get('show/{id}', 'show');
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
            // Route::get('searchProducts', 'searchProducts');
            Route::get('topProducts', 'topProducts');
        });
    });
});
Route::group(['prefix' => 'product'], function () {
    Route::controller(ProductImageController::class)->group(function () {
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::delete('deleteImage/{id}', 'delete');
        });
    });
});
Route::group(['prefix' => 'review'], function () {
    Route::controller(ReviewsController::class)->group(function () {
        Route::middleware('auth')->group(function () {
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });
});

Route::group(['prefix' => 'pack'], function () {
    Route::controller(PackController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('show/{id}', 'show');
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });
});
Route::group(['prefix' => 'promoBanniere'], function () {
    Route::controller(PromoBanniereController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('show/{id}', 'show');
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });
});
Route::group(['prefix' => 'paymentMethode'], function () {
    Route::controller(PaymentMethodeController::class)->group(function () {
        Route::get('/', 'index');
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::post('store', 'store');
            Route::post('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });
});
Route::group(['prefix' => 'promoProduct'], function () {
    Route::controller(PromoProductController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('show/{id}', 'show');
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });
});
Route::group(['prefix' => 'favorite'], function () {
    Route::controller(FavoriteController::class)->group(function () {
        Route::get('/', 'index');
        Route::middleware(['auth'])->group(function () {
            Route::post('/{product}/favorite', 'addFavorite');
            Route::delete('/{product}/favorite', 'removeFavorite');
        });
    });
});


Route::group(['prefix' => 'livraison'], function () {
    Route::controller(LivraisonController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('show/{id}', 'show');
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::post('store', 'store');
            Route::post('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });
});


Route::group([
    'prefix' => 'order'
], function () {
    Route::controller(OrderController::class)->group( function (){

    Route::middleware('auth')->group(function () {
        Route::get('/', 'index')->middleware(CheckRole::class);
        Route::get('/dashboardorder', 'dashboard');
        Route::post('/store', 'store');
        Route::get('/mescommandes', 'getUserOrders');
        Route::post('/updatestatut/{id}', 'updateStatut')->middleware(CheckRole::class);
        Route::get('/detailscommandes/{id}', 'show');
    });
});
});
Route::group([
    'prefix' => 'regle'
], function () {
    Route::controller(RegleController::class)->group( function (){
        Route::get('show/{id}', 'show');
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::post('/update_regle/{id}', 'update');

    });
});
});
Route::group([
    'prefix' => 'socialink'
], function () {
    Route::controller(SocialinkController::class)->group( function (){
        Route::get('/', 'index');
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::post('/store', 'store');
            Route::get('show/{id}', 'show');
        Route::post('/update_socialink/{id}', 'update');
    });
});
});
Route::group([
    'prefix' => 'banniere'
], function () {
    Route::controller(BanniereController::class)->group( function (){
        Route::get('/', 'index');
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::post('/store', 'store');
            Route::get('show/{id}', 'show');
        Route::post('/update_socialink/{id}', 'update');
    });
});
});
Route::group([
    'prefix' => 'dashboard'
], function () {
    Route::controller(DashboardController::class)->group( function (){
        Route::middleware(['auth','role:Admin'])->group(function () {
            Route::get('/', 'index');
    });
});
});

