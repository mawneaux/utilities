<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

class CsvImportController extends Controller {

    protected $reportType;

    public function __construct() {
        //
    }

    public function index() {
        
    }

    public function upload(Request $request) {
        $this->reportType = $request->input('report_type');
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
                if (1) {
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
                $cfg = $this->reportConfig($this->reportType);

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
        return $this->loadData($data);
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
                ],
                'guarded' => ['updated_date', 'updated_time']
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
                ],
                'guarded' => ['patient_id', 'first_name']
            ]
        ];
        return(array_key_exists($report, $reports)) ? $reports[$report] : false;
    }

    protected function loadData($patients) {
        $cfg = $this->reportConfig($this->reportType);
        $guarded = $cfg['guarded'];
        //return $patients;
        $total = count($patients);
        $count = 0;
        $badCount = 0;
        $updated = 0;
        foreach ($patients as $patient) {
            //return $patient;
            $row = \App\Patient::where('patient_id', $patient['patient_id'])->first();
            //return $row;
            if (count($row)) {
                $update = false;
                foreach ($patient as $field => $v) {
                    if (!in_array($field, $guarded)) {
                        $row->$field = $v;
                        $update = true;
                    }
                }
                if ($update) {
                    $affected = $row->update();
                    if ($affected) {
                        $updated++;
                    }
                }
            } else {
                $pt = new \App\Patient();
                $pt->fill($patient)->save();
                if ($pt->id) {
                    $count++;
                } else {
                    $badCount++;
                }
            }
        }
        return ['total_records' => $total, 'records_inserted' => $count, 'records_updated' => $updated, 'bad_count' => $badCount];
    }

}

/*
 * 'ignore' => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 26, 27, 29, 30, 31, 32, 33, 34, 38, 39],
 */