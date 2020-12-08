<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeTutorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_tutor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 127);
            $table->bigInteger('tutor_id')->unsigned();
            $table->bigInteger('uni_id')->unsigned();
            $table->string('phone', 20);
            $table->string('email', 127)->unique();
            $table->string('website');
            $table->string('slug', 127);
            $table->json('images')->nullable();
            $table->boolean('active')->default(1);
            $table->foreign('uni_id')->references('id')->on('universities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tutor_id')->references('id')->on('tutors')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('attribute_tutor', function (Blueprint $table) {
            Schema::drop('attribute_tutor');
            //
        });
    }
}
