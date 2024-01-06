<?php

use App\CustomResultSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomResultSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_result_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_type_id');
            $table->float('exam_percentage');
            $table->string('merit_list_setting');
            $table->integer('academic_year');            
            $table->integer('school_id')->nullable()->default(1)->unsigned();
            $table->foreign('school_id')->references('id')->on('sm_schools')->onDelete('cascade');
            $table->integer('academic_id')->nullable()->default(1)->unsigned();
            $table->foreign('academic_id')->references('id')->on('sm_academic_years')->onDelete('cascade');
            $table->timestamps();
        });

        $store = new CustomResultSetting();
        $store->id = 1;
        $store->merit_list_setting = 'total_mark';
        $store->save();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_result_settings');
    }
}
