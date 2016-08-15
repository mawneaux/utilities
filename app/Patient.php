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

    /**
        * Clean first name set.
          *
          * @param  string $value
          * @return string
          */
    public function setFirstNameAttribute($value) {
        $this->attributes['first_name'] = ($value) ? strtoupper(preg_replace("/[^A-Za-z-\s]/", "", str_replace('<MRG>', '', $value))) : null;
    }

    /**
        * Clean last name set.
          *
          * @param  string $value
          * @return string
          */
    public function setLastNameAttribute($value) {
        $this->attributes['last_name'] = ($value) ? strtoupper(preg_replace("/[^A-Za-z-\s]/", "", str_replace('<MRG>', '', $value))) : null;
    }

}
