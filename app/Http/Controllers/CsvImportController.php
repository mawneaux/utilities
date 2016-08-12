<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

class CsvImportController extends Controller {

    protected $path;
    protected $file;

    public function __construct($path = null, $file = null) {
        $this->path = $path;
        $this->file = $file;
    }

    public function index() {
        
    }

    public function upload(Request $request) {
        $file = $request->file('csv');
        return $this->parsePatientList($file->getPathname());
    }

    protected function readCsv($file, $delimiter = ',') {
        if (!file_exists($file) || !is_readable($file)) {
            return false;
        }
        $header = null;
        $data = array();
        $rowcount = 0;
        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle, 0, $delimiter, '"', '"');
            while ($row = fgetcsv($handle, 0, $delimiter)) {
                if ($rowcount <= 100) {
                    $data[] = $row;
                    $rowcount++;
                }
            }
            fclose($handle);
        }
        return $data;
    }

    protected function parsePatientList($file) {
        $rows = $this->readCsv($file);
        //return $rows;
        $data = [];
        foreach ($rows as $rowk => $row) {
            foreach ($row as $k => $v) {
                $cfg = $this->reportConfig('appointments');
                if (!array_key_exists($rows[$rowk], $cfg['ignore_if_null'])) {
                    if (array_key_exists($k, $cfg['map'])) {
                        $hasFieldName = $cfg['map'][$k]['field_name'];
                        if (!$hasFieldName) {
                            $fields = $cfg['map'][$k]['fields'];
                            $values = explode($cfg['map'][$k]['separator'], $v);
                            $rowCount = 0;
                            foreach ($fields as $field) {
                                if (strlen($v)) {
                                    $data[$rowk][$fields[$rowCount]] = (strlen(trim($values[$rowCount]))) ? trim($values[$rowCount]) : null;
                                } else {
                                    $data[$rowk][$fields[$rowCount]] = null;
                                }
                                $rowCount++;
                            }
                        } else {
                            $data[$rowk][$hasFieldName] = (strlen(trim($v))) ? trim($v) : null;
                        }
                    }
                }
            }
        }
        return $data;
    }

    protected function reportConfig($report) {
        $reports = ['patient_list' => [
                'ignore_if_null' => [16],
                'map' => [
                    16 => ['field_name' => 'patient_id', 'field_type' => 'integer', 'separator' => false, 'fields' => false],
                    17 => ['field_name' => false, 'field_type' => 'string', 'separator' => ';', 'fields' => ['first_name', 'middle', 'last_name', 'suffix']],
                    18 => ['field_name' => 'date_of_birth', 'field_type' => 'date', 'separator' => false, 'fields' => false],
                    19 => ['field_name' => false, 'field_type' => 'string', 'separator' => ';', 'fields' => ['g_first_name', 'g_middle', 'g_last_name', 'g_suffix']],
                    20 => ['field_name' => 'address', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    21 => ['field_name' => false, 'field_type' => 'string', 'separator' => ';', 'fields' => ['city', 'state']],
                    22 => ['field_name' => 'postal_code', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    23 => ['field_name' => 'payer', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    24 => ['field_name' => 'provider', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    25 => ['field_name' => 'balance', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    28 => ['field_name' => false, 'field_type' => 'string', 'separator' => ';', 'fields' => ['phone_number', 'email']],
                    35 => ['field_name' => 'updated_date', 'field_type' => 'date', 'separator' => false, 'fields' => false],
                    36 => ['field_name' => 'updated_time', 'field_type' => 'time', 'separator' => false, 'fields' => false],
                    37 => ['field_name' => 'facility', 'field_type' => 'string', 'separator' => false, 'fields' => false]
                ]
            ],
            'appointments' => [
                'ignore_if_null' => [33],
                'map' => [
                    15 => ['field_name' => false, 'field_type' => 'string', 'separator' => '  ', 'fields' => ['appt_date', 'junk']],
                    31 => ['field_name' => 'appt_time', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    32 => ['field_name' => 'appt_type', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    33 => ['field_name' => 'patient_id', 'field_type' => 'integer', 'separator' => false, 'fields' => false],
                    43 => ['field_name' => false, 'field_type' => 'string', 'separator' => ';', 'fields' => ['status', 'appt_id']],
                    49 => ['field_name' => 'note', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    58 => ['field_name' => 'acct_status', 'field_type' => 'string', 'separator' => false, 'fields' => false],
                    61 => ['field_name' => 'facility', 'field_type' => 'string', 'separator' => false, 'fields' => false]
                ]
            ]
        ];
        return(array_key_exists($report, $reports)) ? $reports[$report] : false;
    }

}

/*
 * appt_date"12/28/2011  12:00:00AM",15

appt_time    " 9:15 AM",31
appt_type           32
patient_id    "1794",33

status    ";14",43

note    "3 wk f/u",49
note2    "** Patient in collections",58
facility    "OSSA",61
 */

/*
 * 'ignore' => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 26, 27, 29, 30, 31, 32, 33, 34, 38, 39],
 */