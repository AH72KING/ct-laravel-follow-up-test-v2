<?php

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'index']);
Route::post('/store', [ProductController::class, 'store']);
Route::post('/update/{id}', [ProductController::class, 'update']);

Route::get('/download-json', function () {
    $filePath = storage_path('app/public/products.json');

    if (!file_exists($filePath)) {
        $message = 'File not found at'.$filePath;
        //dd($message);
        abort(404, $message);
    }

    return Response::download($filePath, 'products.json');
});