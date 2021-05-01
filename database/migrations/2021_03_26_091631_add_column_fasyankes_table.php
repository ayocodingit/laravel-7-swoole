<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFasyankesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fasyankes', function (Blueprint $table) {
            $table->string('kode_fasyankes')->nullable();
            $table->string('nama_fasyankes')->nullable();
            $table->string('jenis_fasyankes')->nullable();
            $table->string('kode_kabupaten_fasyankes')->nullable();
            $table->string('nama_kabupaten_fasyankes')->nullable();
            $table->string('id_fasyankes_pelaporan')->nullable();
            $table->string('nama_fasyankes_pelaporan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fasyankes', function (Blueprint $table) {
            $table->dropColumn('kode_fasyankes');
            $table->dropColumn('nama_fasyankes');
            $table->dropColumn('jenis_fasyankes');
            $table->dropColumn('kode_kabupaten_fasyankes');
            $table->dropColumn('nama_kabupaten_fasyankes');
            $table->dropColumn('id_fasyankes_pelaporan');
            $table->dropColumn('nama_fasyankes_pelaporan');
        });
    }
}
