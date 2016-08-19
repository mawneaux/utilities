<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgingEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aging_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('patient_id')->index();
            $table->bigInteger('visit_id')->index();
            $table->string('payer')->nullable()->index();
            $table->string('payer_group')->nullable()->index();
            $table->string('payer_class')->nullable()->index();
//            $table->decimal('pt_deposit', 20, 2)->default(0.00)->index();
//            $table->decimal('pt_0-30', 20, 2)->default(0.00)->index();
//            $table->decimal('pt_31-60', 20, 2)->default(0.00)->index();
//            $table->decimal('pt_61-90', 20, 2)->default(0.00)->index();
//            $table->decimal('pt_91-120', 20, 2)->default(0.00)->index();
//            $table->decimal('pt_120+', 20, 2)->default(0.00)->index();
//            $table->decimal('pt_total', 20, 2)->default(0.00)->index();
            $table->decimal('ins_deposit', 20, 2)->default(0.00)->index();
            $table->decimal('ins_0-30', 20, 2)->default(0.00)->index();
            $table->decimal('ins_31-60', 20, 2)->default(0.00)->index();
            $table->decimal('ins_61-90', 20, 2)->default(0.00)->index();
            $table->decimal('ins_91-120', 20, 2)->default(0.00)->index();
            $table->decimal('ins_120+', 20, 2)->default(0.00)->index();
            $table->decimal('ins_total', 20, 2)->default(0.00)->index();
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
        Schema::drop('aging_entries');
    }
}
/*/
 * 12 => ['field_name' => 'patient_id', 'field_type' => 'integer', 'separator' => false, 'fields' => false],
                    13 => ['field_name' => false, 'field_type' => 'string', 'separator' => ';', 'fields' => ['skip', 'insurance']],
                    15 => ['field_name' => 'pt_deposit', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    16 => ['field_name' => 'pt_0-30', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    17 => ['field_name' => 'pt_31-60', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    18 => ['field_name' => 'pt_61-90', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    19 => ['field_name' => 'pt_91-120', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    20 => ['field_name' => 'pt_120+', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    21 => ['field_name' => 'pt_total', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    31 => ['field_name' => 'ins_deposit', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    32 => ['field_name' => 'ins_0-30', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    33 => ['field_name' => 'ins_31-60', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    34 => ['field_name' => 'ins_61-90', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    35 => ['field_name' => 'ins_91-120', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    36 => ['field_name' => 'ins_120+', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    37 => ['field_name' => 'ins_total', 'field_type' => 'string', 'separator' => false, 'fields' => false]
 *
 */