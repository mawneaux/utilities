<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['appt_id', 'patient_visit_id', 'appt_type', 'patient_id', 'facility', 'date', 'time', 'financial_class', 'status', 'acct_status', 'note'];
    /**
        * Clean date set.
          *
          * @param  string $value
          * @return string
          */
    public function setDateAttribute($value) {
        $this->attributes['date'] = ($value) ? date('Y-m-d', strtotime($value)) : null;
    }
    /**
        * Clean time set.
          *
          * @param  string $value
          * @return string
          */
    public function setTimeAttribute($value) {
        $this->attributes['time'] = ($value) ? date('H:i:s', strtotime($value)) : null;
    }
}
