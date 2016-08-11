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
        return dd($request->file('csv')->getClientOriginalName());
        $file = $request->file('csv');
        $contents = file_get_contents($file->filename);
        return $contents;
    }

    protected function readCsv($delimiter = ',') {
        if (!file_exists($this->path . '/' . $this->file) || !is_readable($this->path . '/' . $this->file)) {
            return false;
        }
        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    protected function importCsv() {
        $file = public_path($path . '/' . $file);

        $customerArr = $this->csvToArray($file);

        for ($i = 0; $i < count($customerArr); $i ++) {
            User::firstOrCreate($customerArr[$i]);
        }

        return 'Jobi done or what ever';
    }

}
