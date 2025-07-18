<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ServiceTemplateController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AuthController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('home');

    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Customer routes
    Route::resource('customers', CustomerController::class);
    Route::patch('customers/{customer}/status', [CustomerController::class, 'updateStatus'])->name('customers.update-status');

    Route::resource('receipts', ReceiptController::class);
    Route::get('receipts/{id}/print', [ReceiptController::class, 'print'])->name('receipts.print');

    // Quote routes
    Route::resource('quotes', QuoteController::class);
    Route::patch('quotes/{quote}/status', [QuoteController::class, 'updateStatus'])->name('quotes.status.update');
    Route::get('quotes/{quote}/print', [QuoteController::class, 'print'])->name('quotes.print');

    // Service Template routes
    Route::resource('service-templates', ServiceTemplateController::class);

    // Subscription routes
    Route::resource('subscriptions', SubscriptionController::class);
    Route::patch('subscriptions/{subscription}/status', [SubscriptionController::class, 'updateStatus'])->name('subscriptions.updateStatus');
    Route::patch('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew'])->name('subscriptions.renew');
    Route::post('subscriptions/{subscription}/test-warning', [SubscriptionController::class, 'sendTestWarning'])->name('subscriptions.test-warning');
    Route::post('subscriptions/{subscription}/test-expired', [SubscriptionController::class, 'sendTestExpired'])->name('subscriptions.test-expired');
    Route::get('service-templates/{serviceTemplate}/details', [SubscriptionController::class, 'getServiceTemplateDetails'])->name('service-templates.details');
});