<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gender');
            $table->date('dob');
            $table->string('phone_number');
            $table->string('alternate_phone_number')->nullable();
            $table->string('email');
            $table->string('address');
            $table->string('qualification');
            $table->string('course');
            $table->string('optional_subject')->nullable();
            $table->integer('attempts_given');
            $table->string('referral_source');
            $table->string('counseling_satisfaction');
            $table->boolean('contact_preference');
            $table->string('status');
            $table->date('rescheduled_date')->nullable();
            $table->unsignedBigInteger('counsellor_id')->nullable();
            $table->foreign('counsellor_id')->references('id')->on('users')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->string('dp_path', 255)->nullable();
            $table->softDeletes(); // For soft deletes
            $table->timestamps();
            $table->unique(['email', 'phone_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enquiries');
    }
};
