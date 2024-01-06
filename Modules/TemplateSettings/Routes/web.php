<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('templatesettings')->middleware('subdomain')->group(function() {
    Route::get('/', 'TemplateSettingsController@index');
    Route::get('/about', 'TemplateSettingsController@about');
    
    Route::get('email-template', 'TemplateSettingsController@emailTemplate')->name('templatesettings.email-template')->middleware('userRolePermission:480');
    Route::post('email-template', 'TemplateSettingsController@emailTemplateUpdate')->name('templatesettings.email-template');

    Route::get('sms-template', 'TemplateSettingsController@smsTemplate')->name('templatesettings.sms-template')->middleware('userRolePermission:710');
    Route::post('sms-template-update', 'TemplateSettingsController@smsTemplateUpdate')->name('templatesettings.sms-template-update')->middleware('userRolePermission:711');
});