<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTutorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tutors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name', 127);
            $table->string('last_name', 127);
            $table->string('phone', 20);
            $table->string('email', 127)->unique();
            $table->string('password');
            $table->bigInteger('uni_id')->unsigned();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->text('address')->nullable();
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
        Schema::table('tutors', function (Blueprint $table) {
            Schema::drop('tutors');
            //
        });
    }
}
