<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUniversitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 127);
            $table->string('phone', 20);
            $table->string('slug', 127);
            $table->string('email', 127)->unique();
            $table->string('password');
            $table->json('images')->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('approved')->default(0);
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
        Schema::table('universities', function (Blueprint $table) {
            Schema::drop('universities');
        });
    }
}
