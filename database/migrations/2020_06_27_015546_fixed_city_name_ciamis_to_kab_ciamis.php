<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixedCityNameCiamisToKabCiamis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kota', function (Blueprint $table) {
            \DB::table('kota')->where('id', '3207')->update(['nama' => 'KAB CIAMIS']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kota', function (Blueprint $table) {
            //
        });
    }
}
