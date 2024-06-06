<?php

use App\Http\Controllers\VoiceRecordController;
use Illuminate\Support\Facades\Route;

Route::get('/',[VoiceRecordController::class,'index'])->name('home');
Route::post('/save_temp',[VoiceRecordController::class,'save_temp']);
Route::post('/store',[VoiceRecordController::class,'store'])->name('save_voice');