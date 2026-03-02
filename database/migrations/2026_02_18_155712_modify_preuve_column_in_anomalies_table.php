<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('anomalies', function (Blueprint $table) {
            $table->text('preuve')->change();
        });
    }

    public function down()
    {
        Schema::table('anomalies', function (Blueprint $table) {
            $table->string('preuve')->change();
        });
    }
};
