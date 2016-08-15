<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('appt_id')->unique();
            $table->string('appt_type', 255)->nullable()->index();
            $table->bigInteger('patient_id')->nullable()->index();
            $table->string('facility')->nullable()->index();
            $table->date('date')->nullable()->index();
            $table->time('time')->nullable()->index();
            $table->string('status', 255)->nullable();
            $table->text('acct_status')->nullable();
            $table->text('note')->nullable();
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
        Schema::drop('appointments');
    }
}

/*/
 *     "date": "4/1/2016",
    "time": "9:30 AM",
    "patient_id": "16355",
    "appt_type": "IS Follow Up",
    "status": "Completed",
    "appt_id": "136504",
    "note": "Follow up From LESI JKF",
    "acct_status": "** Patient in collections",
    "facility": "OSSA"
 */
