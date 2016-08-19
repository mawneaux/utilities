<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgingEntry extends Model {

    protected $fillable = ['patient_id', 'visit_id', 'payer', 'payer_group', 'payer_class', 'pt_deposit', 'pt_0-30', 'pt_31-60', 'pt_61-90', 'pt_91-120', 'pt_120+', 'pt_total', 'ins_deposit', 'ins_0-30', 'ins_31-60', 'ins_61-90', 'ins_91-120', 'ins_120+', 'ins_total'];

}
