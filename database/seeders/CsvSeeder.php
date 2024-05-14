<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

abstract class CsvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected const name = 0;
    protected const code = 1;
    protected const status = 2;

    /**
     * Run the database seeds.
     */

    protected function readCSV($csvFile, $array)
    {
        $file_handle = fopen($csvFile, 'r');
        $line_of_text = [];
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }
        fclose($file_handle);
        return $line_of_text;
    }
}
