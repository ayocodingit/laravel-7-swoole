<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSwabAntigensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('swab_antigen', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pasien')->index();
            $table->string('nomor_registrasi')->index()->unique();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('usia_tahun')->nullable();
            $table->integer('usia_bulan')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('no_telp', 16)->nullable();
            $table->enum('warganegara', ['WNA', 'WNI'])->nullable();
            $table->string('negara_asal')->nullable();
            $table->enum('jenis_identitas', ['ktp', 'paspor'])->nullable();
            $table->string('no_identitas', 16);
            $table->string('kategori')->nullable()->index();
            $table->string('alamat')->nullable();
            $table->foreignId('kode_provinsi')->nullable()->constrained('provinsi');
            $table->foreignId('kode_kota_kabupaten')->nullable()->constrained('kota');
            $table->foreignId('kode_kecamatan')->nullable()->constrained('kecamatan');
            $table->foreignId('kode_kelurahan')->nullable()->constrained('kelurahan');
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->string('kondisi_pasien')->nullable();
            $table->date('tanggal_gejala')->nullable();
            $table->string('jenis_gejala')->nullable();
            $table->integer('tujuan_pemeriksaan')->index();
            $table->string('tujuan_pemeriksaan_lainnya')->nullable();
            $table->string('jenis_antigen')->index()->nullable();
            $table->string('no_hasil')->index();
            $table->date('tanggal_periksa')->index()->nullable();
            $table->string('hasil_periksa')->index()->nullable();
            $table->date('tanggal_validasi')->index()->nullable();
            $table->foreignId('validator_id')->nullable()->constrained('validator');
            $table->foreignId('valid_file_id')->nullable()->constrained('files');
            $table->enum('status', ['registrasi', 'tervalidasi'])->default('registrasi');
            $table->datetime('waktu_sampel_print')->nullable();
            $table->integer('counter_print_hasil')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('swab_antigen', function (Blueprint $table) {
            $table->dropForeign('swab_antigen_kode_provinsi_foreign');
            $table->dropForeign('swab_antigen_kode_kota_kabupaten_foreign');
            $table->dropForeign('swab_antigen_kode_kecamatan_foreign');
            $table->dropForeign('swab_antigen_kode_kelurahan_foreign');
            $table->dropForeign('swab_antigen_validator_id_foreign');
            $table->dropForeign('swab_antigen_valid_file_id_foreign');
        });
        Schema::dropIfExists('swab_antigen');
    }
}
