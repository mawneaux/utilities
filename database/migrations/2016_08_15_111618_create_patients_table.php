<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('patients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('patient_id')->unique();
            $table->string('first_name')->nullable();
            $table->string('middle')->nullable();
            $table->string('last_name')->nullable();
            $table->string('suffix')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('facility')->nullable();
            $table->string('payer')->nullable();
            $table->string('provider')->nullable();
            $table->string('balance')->nullable();
            $table->string('g_first_name')->nullable();
            $table->string('g_middle')->nullable();
            $table->string('g_last_name')->nullable();
            $table->string('g_suffix')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('patients');
    }

}

/*/
 * patient_id": "13687",
"first_name": "*Antonia",
"middle": null,
"last_name": "*Banks",
"suffix": null,
"date_of_birth": "06/15/1985",
"g_first_name": null,
"g_middle": null,
"g_last_name": null,
"g_suffix": null,
"address": null,
"city": null,
"state": null,
"postal_code": null,
"payer": "Finance Company",
"provider": null,
"balance": "$0.00",
"phone_number": "4047965702",
"email": null,
"updated_date": "08/12/2016",
"updated_time": "9:13 am",
"facility": "OSSA"
 */