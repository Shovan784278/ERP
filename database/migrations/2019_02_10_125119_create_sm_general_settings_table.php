<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_general_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('school_name')->nullable();
            $table->string('site_title')->nullable();
            $table->string('school_code')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('file_size')->default(102400);
            $table->string('currency')->nullable()->default('USD');
            $table->string('currency_symbol')->nullable()->default('$');
            $table->integer('promotionSetting')->nullable()->default(0);
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('system_version')->nullable()->default('6.6.1');
            $table->integer('active_status')->nullable()->default(1);
            $table->string('currency_code')->nullable()->default('USD');
            $table->string('language_name')->nullable()->default('en');
            $table->string('session_year')->nullable()->default('2020');
            $table->string('system_purchase_code')->nullable();
            $table->date('system_activated_date')->nullable();
            $table->date('last_update')->nullable();
            $table->string('envato_user')->nullable();
            $table->string('envato_item_id')->nullable();
            $table->string('system_domain')->nullable();
            $table->string('copyright_text')->nullable();
            $table->integer('api_url')->default(1);
            $table->integer('website_btn')->default(1);
            $table->integer('dashboard_btn')->default(1);
            $table->integer('report_btn')->default(1);
            $table->integer('style_btn')->default(1);
            $table->integer('ltl_rtl_btn')->default(1);
            $table->integer('lang_btn')->default(1);
            $table->string('website_url')->nullable();
            $table->integer('ttl_rtl')->default(2);
            $table->integer('phone_number_privacy')->default(1)->comments('1=enable, 0=disable');
            $table->timestamps();
            $table->integer('week_start_id')->nullable();
            $table->integer('time_zone_id')->nullable();
            $table->integer('attendance_layout')->default(1)->nullable();
            $table->integer('session_id')->nullable()->unsigned();
            $table->foreign('session_id')->references('id')->on('sm_academic_years')->onDelete('set null');

            $table->integer('language_id')->nullable()->default(1)->unsigned();
            $table->foreign('language_id')->references('id')->on('sm_languages')->onDelete('set null');

            $table->integer('date_format_id')->nullable()->default(1)->unsigned();
            $table->foreign('date_format_id')->references('id')->on('sm_date_formats')->onDelete('set null');

            $table->integer('ss_page_load')->nullable()->default(3);

            $table->integer('school_id')->nullable()->default(1)->unsigned();
            $table->foreign('school_id')->references('id')->on('sm_schools')->onDelete('cascade');
            $table->string('software_version', 100)->nullable();
            $table->string('email_driver')->default('php');

            $table->text('fcm_key')->nullable();


            $table->integer('Lesson')->default(1)->nullable();
            $table->integer('Chat')->default(1)->nullable();
            $table->integer('FeesCollection')->default(0)->nullable();
            $table->integer('income_head_id')->default(0)->nullable();
            $table->integer('InfixBiometrics')->default(0)->nullable();
            $table->integer('ResultReports')->default(0)->nullable();
            $table->integer('TemplateSettings')->default(1)->nullable();
            $table->integer('MenuManage')->default(1)->nullable();
            $table->integer('RolePermission')->default(1)->nullable();
            $table->integer('RazorPay')->default(0)->nullable();
            $table->integer('Saas')->default(1)->nullable();
            $table->integer('StudentAbsentNotification')->default(1)->nullable();
            $table->integer('ParentRegistration')->default(0)->nullable();
            $table->integer('Zoom')->default(0)->nullable();
            $table->integer('BBB')->default(0)->nullable();
            $table->integer('VideoWatch')->default(0)->nullable();
            $table->integer('Jitsi')->default(0)->nullable();
            $table->integer('OnlineExam')->default(0)->nullable();
            $table->integer('SaasSubscription')->default(0)->nullable();
            $table->integer('SaasRolePermission')->default(0)->nullable();
            $table->integer('BulkPrint')->default(1)->nullable();
            $table->integer('HimalayaSms')->default(1)->nullable();
            $table->integer('XenditPayment')->default(1)->nullable();
            $table->integer('Wallet')->default(1)->nullable();
            $table->integer('Lms')->default(0)->nullable();
            $table->integer('University')->default(0)->nullable();
            $table->integer('fees_status')->default(1)->nullable();
            $table->integer('lms_checkout')->default(0)->nullable();
            $table->integer('academic_id')->nullable()->unsigned();
            $table->foreign('academic_id')->references('id')->on('sm_academic_years')->onDelete('set null');
        });




        DB::table('sm_general_settings')->insert([
            [
                'copyright_text' => 'Copyright &copy; 2020 All rights reserved | This application is made by Codethemes',
                'logo' => 'public/uploads/settings/logo.png',
                'favicon' => 'public/uploads/settings/favicon.png',
                'phone' => '+8801841412141',
                'school_code' => '12345678',
                'email' => 'info@spondonit.com',
                'address' => '89/2 Panthapath, Dhaka 1215, Bangladesh',
                'currency' => 'USD',
                'currency_symbol' => '$',
                'school_name' => 'Infix Edu',
                'site_title' => 'Infix Education software',
                'session_id' => 1,
                'time_zone_id' => 51,
                'software_version' => '6.6.1',
                'system_activated_date' => date('Y-m-d'),
                'last_update' => date('Y-m-d'),
                'system_domain' => url('/'),
                'email_driver'=>"php",
                'income_head_id'=>1,
                'academic_id'=>1
            ]
        ]);

        


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_general_settings');
    }
}