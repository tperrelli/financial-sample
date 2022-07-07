<?php

namespace App\Http\Controllers\Api;

use App\Jobs\DebitProcessor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class CSVReceiverController extends Controller
{
    /**
     * Receives a request with csv
     * 
     * @param Illuminate\Http\Request
     */
    public function __invoke(Request $request)
    {
        $rules = [
            'csv' => 'file|required'
        ];

        $this->validate($request, $rules);

        $csvFile = $request->file('csv');
        $path = $csvFile->path();

        if (($open = fopen($path, 'r')) !== FALSE) {
            $headers = fgetcsv($open, 1000, ",");

            while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {

                $record = array_combine($headers,$data);
                dispatch(new DebitProcessor($record));
            }

            fclose($open);
        }

        return new JsonResource([
            'message' => 'The CSV file was processed;'
        ]);
    }
}
