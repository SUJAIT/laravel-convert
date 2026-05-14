<?php

use App\Http\Controllers\ServerCopyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Server Copy API Routes
|--------------------------------------------------------------------------
|
| Add these lines to your routes/api.php file:
|
|   require __DIR__ . '/server_copy.php';
|
| Or copy the routes directly into api.php.
|
*/

// No auth middleware — public endpoints (add auth()->middleware() here if needed later)

Route::prefix('server-copy')->group(function () {

    // Search: GET /api/server-copy/search?nid=1234567890&dob=1990-01-01
    Route::get('/search', [ServerCopyController::class, 'search'])
        ->name('server-copy.search');

    // PDF download: GET /api/server-copy/pdf?nid=1234567890&dob=1990-01-01
    Route::get('/pdf', [ServerCopyController::class, 'downloadPdf'])
        ->name('server-copy.pdf');

});
