<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_student', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 127);
            $table->bigInteger('student_id')->unsigned();
            $table->string('email', 127)->unique();
            $table->string('resume');
            $table->bigInteger('uni_id')->unsigned();
            $table->json('images')->nullable();
            $table->boolean('active')->default(1);
            $table->foreign('student_id')->references('id')->on('students')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('uni_id')->references('id')->on('universities')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('attribute_student', function (Blueprint $table) {
            Schema::drop('attribute_student');
            //
        });
    }
}
