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
                if ($rowcount <= 730) {
                    $data[] = array_combine($header, $row);
                    $rowcount++;
                }
            }
            fclose($handle);
        }
        return $data;
    }

    protected function parsePatientList($file) {
        $data = $this->readCsv($file);
        foreach ($data as $k => $v) {
            foreach ($v as $k2 => $v2) {
                if (in_array($k2, ['patient_last_name', 'guarantor_last_name']) && stristr($v2, '"')) {
                    $tmp2 = explode('_', $k2);
                    $tmp = explode('",', $v2);
                    $data[$k][$k2] = (array_key_exists(0, $tmp)) ? str_replace('"', '', $tmp[0]) : null;
                    $data[$k][$tmp2[0] . '_first_name'] = (array_key_exists(1, $tmp)) ? trim($tmp[1]) : null;
                    if ($data[$k][$k2] !== null) {
                        $tmp = explode(',', $data[$k][$k2]);
                        $data[$k][$k2] = trim($tmp[0]);
                        $data[$k][$tmp2[0] . '_suffix'] = (strlen(trim($tmp[1]))) ? trim($tmp[1]) : null;
                    }
                } elseif (in_array($k2, ['patient_last_name', 'guarantor_last_name']) && !stristr($v2, '"')) {
                    $tmp2 = explode('_', $k2);
                    $tmp = explode(',', $v2);
                    $data[$k][$k2] = (array_key_exists(0, $tmp)) ? trim(str_replace('"', '', $tmp[0])) : null;
                    $data[$k][$tmp2[0] . '_first_name'] = (array_key_exists(1, $tmp)) ? trim($tmp[1]) : null;
                } else {
                    if (strlen(trim($v2)) && $k2 === 'date_of_birth') {
                        $v2 = date('Y-m-d', strtotime($v2));
                    }
                    if (strlen(trim($v2)) && $k2 === 'balance') {
                        $v2 = trim(str_replace('(', '-', str_replace('$', '', str_replace(')', '', $v2))));
                    }
                    $data[$k][$k2] = trim(str_replace('  ', ' ', $v2));
                }
            }
        }
        return $data;
    }

}
