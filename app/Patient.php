<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model {

    protected $fillable = ['patient_id', 'first_name', 'middle', 'last_name', 'suffix', 'date_of_birth', 'address', 'city', 'state', 'postal_code', 'phone_number', 'email', 'facility', 'payer', 'provider', 'balance', 'g_first_name', 'g_middle', 'g_last_name', 'g_suffix'];

    /**
        * Clean date of birth set.
          *
          * @param  string $value
          * @return string
          */
    public function setDateOfBirthAttribute($value) {
        $this->attributes['date_of_birth'] = ($value) ? date('Y-m-d', strtotime($value)) : null;
    }

}
