<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('distributed_kv_entries', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable(); // per soft delete logica
        });

    }

    public function down()
    {
        Schema::dropIfExists('distributed_kv_entries');
    }
};
