<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTutorAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tutor_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('question_id')->unsigned();
            $table->bigInteger('tutor_id')->unsigned();
            $table->string('answer');
            $table->boolean('approved')->default(0);
            $table->foreign('question_id')->references('id')->on('student_queries')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('tutor_answers', function (Blueprint $table) {
            Schema::drop('tutor_answers');
        });
    }
}
