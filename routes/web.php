<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route as Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function() {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('module/chat-firebase', 'ChatController@index');

    Route::prefix('module/chat-firebase/instruction/faqs')->group(function() {
        Route::get('', 'InstructionController@index')->name('faqs');
        Route::get('/add', 'InstructionController@faqAdd')->name('faq.add');
        Route::post('/add/submit', 'InstructionController@faqAddSubmit')->name('faq.add.submit');
        Route::get('/edit/{id}', 'InstructionController@faqEdit')->name('faq.edit');
        Route::post('/edit/submit', 'InstructionController@faqEditSubmit')->name('faq.edit.submit');
        Route::post('/delete/submit', 'InstructionController@faqDeleteSubmit')->name('faq.delete.submit');
    });

    Route::prefix('module/chat-firebase/instruction')->group(function() {
        Route::get('', 'InstructionController@index')->name('instructions');
        Route::get('/add', 'InstructionController@instructionAdd')->name('instruction.add');
        Route::post('/add/submit', 'InstructionController@instructionAddSubmit')->name('instruction.add.submit');
        Route::get('/edit/{id}', 'InstructionController@instructionEdit')->name('instruction.edit');
        Route::post('/edit/submit', 'InstructionController@instructionEditSubmit')->name('instruction.edit.submit');
        Route::post('/delete/submit', 'InstructionController@instructionDeleteSubmit')->name('instruction.delete.submit');
        Route::post('/collection', 'InstructionController@getDatabase')->name('instruction.database.data');
    });
});

Route::get('/module/chat-firebase/iframe', function() {
    return view('chat.iframe');
});
