<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_queries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('question', 127);
            $table->bigInteger('student_id')->unsigned();
            $table->bigInteger('subject_id')->unsigned();
            $table->bigInteger('uni_id')->unsigned();
            $table->boolean('active')->default(1);
            $table->boolean('approved')->default(0);
            $table->foreign('student_id')->references('id')->on('students')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('student_queries', function (Blueprint $table) {
            Schema::drop('student_queries');
        });
    }
}
