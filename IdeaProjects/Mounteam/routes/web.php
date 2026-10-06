<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return view('index');
});

use App\Http\Controllers\ContactController;

Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

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

// Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registration routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Logout route (for authenticated users)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Service pages
Route::prefix('services')->name('services.')->group(function () {
    Route::get('/websites', [ServiceController::class, 'websites'])->name('websites');
    Route::get('/design', [ServiceController::class, 'design'])->name('design');
    Route::get('/hosting', [ServiceController::class, 'hosting'])->name('hosting');
    Route::get('/domains', [ServiceController::class, 'domains'])->name('domains');
    Route::get('/promotion', [ServiceController::class, 'promotion'])->name('promotion');
    Route::get('/ai', [ServiceController::class, 'ai'])->name('ai');
    Route::get('/education', [ServiceController::class, 'education'])->name('education');
    Route::get('/payment', [ServiceController::class, 'payment'])->name('payment');
    Route::get('/vpn', [ServiceController::class, 'vpn'])->name('vpn');
});

// Alternative routes for navigation (if you prefer direct routes instead of services prefix)
Route::get('/sites', [ServiceController::class, 'websites'])->name('sites');
Route::get('/design', [ServiceController::class, 'design'])->name('design');
Route::get('/hosting', [ServiceController::class, 'hosting'])->name('hosting');
Route::get('/domains', [ServiceController::class, 'domains'])->name('domains');
Route::get('/promotion', [ServiceController::class, 'promotion'])->name('promotion');
Route::get('/ai', [ServiceController::class, 'ai'])->name('ai');
Route::get('/education', [ServiceController::class, 'education'])->name('education');
Route::get('/payment', [ServiceController::class, 'payment'])->name('payment');
Route::get('/vpn', [ServiceController::class, 'vpn'])->name('vpn');

// Portfolio
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
Route::get('/portfolio/{project}', [PortfolioController::class, 'show'])->name('portfolio.show');

// Projects (requires authentication)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Project management
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/create', [ProjectController::class, 'create'])->name('create');
        Route::post('/', [ProjectController::class, 'store'])->name('store');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
        Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
    });
});

// API routes for AJAX requests (optional)
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/stats', [HomeController::class, 'getStats'])->name('stats');
    Route::post('/contact', [HomeController::class, 'contact'])->name('contact');
    Route::post('/quote', [ProjectController::class, 'getQuote'])->name('quote');
});

// Admin routes (if needed)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::resource('users', 'UserController');
    Route::resource('projects', 'AdminProjectController');
    Route::resource('services', 'AdminServiceController');
});

// Fallback route for 404 pages
Route::fallback(function () {
    return view('errors.404');
});
